vcl 4.0;

import std;
import xkey;
import cookie;
import var;

include "./_config.vcl";
include "./fos_tags_xkey.vcl";
include "./fos_debug.vcl";

sub vcl_recv {

  # Headers used between restarts must never be taken over from a client
  if (req.restarts == 0) {
    unset req.http.X-Cache-Phase;
    unset req.http.X-Cache-Security-Delegate;
    unset req.http.X-Cache-Grant;
    unset req.http.X-Cache-Denied;
    unset req.http.X-Cache-Original-Url;
    unset req.http.X-Cache-Preflight;
  }

  var.set("originalUrl", req.http.X-Forwarded-Prefix + req.url);

  if(var.get("originalUrl") ~ "^/api/varnish/healthcheck") {
    return(synth(200,"OK"));
  }

  # Support xkey purge requests
  # see https://raw.githubusercontent.com/varnish/varnish-modules/master/src/vmod_xkey.vcc
  call fos_tags_xkey_recv;

  if (req.http.X-Cache-Phase != "preflight") {
    # exclude API documentation, profiler and graphql endpoint
    if (var.get("originalUrl") ~ "^/api/docs"
      || var.get("originalUrl") ~ "^/api/graphql"
      || var.get("originalUrl") ~ "^/api/bundles"
      || var.get("originalUrl") ~ "^/api/contexts"
      || var.get("originalUrl") ~ "^/api/_profiler"
      || var.get("originalUrl") ~ "^/api/_wdt") {
      return(pass);
    }

    # exclude any format other than HAL
    if (req.url !~ "\.jsonhal$" && req.http.Accept !~ "application/hal\+json"){
      return(pass);
    }

    # exclude any request with query parameters, until cache handling of query params is properly implemented
    if (req.url ~ "\?"){
      return(pass);
    }
  }

  # The user was denied access to the security delegate of the cached response, let the API answer
  if (req.http.X-Cache-Denied) {
    return(pass);
  }

   # Extract JWT cookie for later use in vcl_hash
   # Failsafe: Pass cache if JWT cookie is not set (also for example, if COOKIE_PREFIX is not properly configured)
  if (req.http.Cookie) {
    cookie.parse(req.http.Cookie);
    cookie.keep(std.getenv("COOKIE_PREFIX") + "jwt_hp," + std.getenv("COOKIE_PREFIX") + "jwt_s");

    if(cookie.get_string() == ""){
      return(pass);
    }

    var.set("JWT", cookie.get_string());
  }

  # Ask the API whether the user may read the security delegate of a cached response (see vcl_hit)
  if (req.http.X-Cache-Phase == "preflight") {
    set req.http.X-Cache-Original-Url = req.url;
    set req.url = "/_cache_access";
  }
}

sub vcl_hash {
  # Responses are shared between users, access to them is checked in vcl_hit. Whether access is granted
  # is specific to the user and security delegate.
  if (req.http.X-Cache-Phase == "preflight") {
    hash_data(var.get("JWT"));
    hash_data(req.http.X-Cache-Security-Delegate);
  }

  # using URL (=path), but not using Host/ServerIP; this allows to share cache between print & normal API calls
  hash_data(req.url);

  return(lookup);
}

sub vcl_hit {
  if (req.http.X-Cache-Phase == "preflight" || obj.http.X-Cache-Security-Delegate == "public") {
    return(deliver);
  }

  # Never serve a response that doesn't state who may read it
  if (!obj.http.X-Cache-Security-Delegate) {
    return(pass);
  }

  if (req.http.X-Cache-Grant && req.http.X-Cache-Grant == obj.http.X-Cache-Security-Delegate) {
    return(deliver);
  }

  set req.http.X-Cache-Phase = "preflight";
  set req.http.X-Cache-Security-Delegate = obj.http.X-Cache-Security-Delegate;
  return(restart);
}

sub vcl_req_cookie {
  # Varnish by default disables caching whenever the request header "Cookie" is set in the request (default safe behavior)
  # this bypasses the default behaviour; this is safe because cached responses are only delivered after checking
  # that the user may read them (see vcl_hit), and the Cookie is passed on to the API when fetching a response
  return (hash);
}

sub vcl_backend_fetch {
  unset bereq.http.X-Cache-Grant;
  unset bereq.http.X-Cache-Denied;
  unset bereq.http.X-Cache-Preflight;
  unset bereq.http.X-Cache-Original-Url;
}

sub vcl_backend_response {
  if (bereq.uncacheable) {
    return (deliver);
  }

  if (bereq.http.X-Cache-Phase == "preflight") {
    # only cache the decision to grant access
    if (beresp.status != 200 || beresp.http.X-Cache-Access != "granted") {
      set beresp.uncacheable = true;
      set beresp.ttl = 120s;
      return (deliver);
    }
  } else if (beresp.status != 200 || !beresp.http.X-Cache-Security-Delegate) {
    # only share successful responses, which state who may read them
    set beresp.uncacheable = true;
    set beresp.ttl = 120s;
    return (deliver);
  }

  call vcl_beresp_stale;

  # Varnish by default disables caching whenever the reponse header "Set-Cookie" is set in the request (default safe behavior)
  # commenting the following line bypasses the default behaviour
  # call vcl_beresp_cookie;

  call vcl_beresp_control;
  call vcl_beresp_vary;

  # a grant must not be served after it expired
  if (bereq.http.X-Cache-Phase == "preflight") {
    set beresp.grace = 0s;
    set beresp.keep = 0s;
  }

  return (deliver);
}

sub vcl_deliver {
  # Continue the original request with the answer of the API
  if (req.http.X-Cache-Phase == "preflight") {
    if (resp.status == 200 && resp.http.X-Cache-Access == "granted") {
      set req.http.X-Cache-Grant = req.http.X-Cache-Security-Delegate;
    } else {
      set req.http.X-Cache-Denied = "true";
    }

    if (obj.hits > 0) {
      set req.http.X-Cache-Preflight = "HIT";
    } else {
      set req.http.X-Cache-Preflight = "MISS";
    }

    set req.url = req.http.X-Cache-Original-Url;
    unset req.http.X-Cache-Phase;
    unset req.http.X-Cache-Security-Delegate;
    unset req.http.X-Cache-Original-Url;
    return (restart);
  }

  call fos_tags_xkey_deliver;
  call fos_debug_deliver;

  if (resp.http.X-Cache-Debug && req.http.X-Cache-Preflight) {
    set resp.http.X-Cache-Preflight = req.http.X-Cache-Preflight;
  }
  unset resp.http.X-Cache-Security-Delegate;

  # reset cache control header to avoid caching by any other upstream proxies
  if (resp.http.Content-Type ~ "application/hal\+json"){
    set resp.http.Cache-Control = "no-cache, private";
  }
}
