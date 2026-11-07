vcl 4.0;

import std;
import xkey;
import cookie;
import var;
import digest;

include "./_config.vcl";
include "./fos_tags_xkey.vcl";
include "./fos_debug.vcl";
include "./purge_all.vcl";

sub vcl_recv {

  # Headers used between restarts must never be taken over from a client
  if (req.restarts == 0) {
    unset req.http.X-Cache-Phase;
    unset req.http.X-Cache-Check-Iri;
    unset req.http.X-Cache-Grant;
    unset req.http.X-Cache-Denied;
    unset req.http.X-Cache-Original-Url;
    unset req.http.X-Cache-Access-Check;
  }

  # Never taken over from a client, it is derived from the login below
  unset req.http.X-Cache-User;

  var.set("originalUrl", req.http.X-Forwarded-Prefix + req.url);

  if(var.get("originalUrl") ~ "^/api/varnish/healthcheck") {
    return(synth(200,"OK"));
  }

  # Support xkey purge requests
  # see https://raw.githubusercontent.com/varnish/varnish-modules/master/src/vmod_xkey.vcc
  call fos_tags_xkey_recv;
  call purge_all_recv;

  if (req.http.X-Cache-Phase != "access_check") {
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
    set req.http.X-Cache-User = digest.hash_sha256(var.get("JWT"));
  }
}

sub vcl_hash {
  # Responses are shared between users, access to them is checked in vcl_hit. Whether access is granted
  # is specific to the user and the security delegate, which is part of the URL of the access check.
  if (req.http.X-Cache-Phase == "access_check") {
    hash_data(var.get("JWT"));
  }

  # using URL (=path), but not using Host/ServerIP; this allows to share cache between print & normal API calls
  hash_data(req.url);

  return(lookup);
}

sub vcl_hit {
  if (req.http.X-Cache-Phase == "access_check") {
    return(deliver);
  }

  # A response cached per user is only found by the user it was fetched for
  if (obj.http.X-Cache-Mode == "public" || obj.http.X-Cache-Mode == "user") {
    return(deliver);
  }

  # Never serve a response that doesn't state who may read it
  if (obj.http.X-Cache-Mode != "shared" || !obj.http.X-Cache-Security-Delegate) {
    return(pass);
  }

  if (req.http.X-Cache-Grant && req.http.X-Cache-Grant == obj.http.X-Cache-Security-Delegate) {
    return(deliver);
  }

  # Ask the API whether the user may read the security delegate, and continue with the original request afterwards
  set req.http.X-Cache-Phase = "access_check";
  set req.http.X-Cache-Check-Iri = obj.http.X-Cache-Security-Delegate;
  set req.http.X-Cache-Original-Url = req.url;
  set req.url = "/authorization/check_access?iri=" + obj.http.X-Cache-Security-Delegate;
  return(restart);
}

sub vcl_req_cookie {
  # Varnish by default disables caching whenever the request header "Cookie" is set in the request (default safe behavior)
  # this bypasses the default behaviour; this is safe because cached responses are only delivered after checking
  # that the user may read them (see vcl_hit), and the Cookie is passed on to the API when fetching a response
  return (hash);
}

sub vcl_backend_fetch {
  unset bereq.http.X-Cache-Check-Iri;
  unset bereq.http.X-Cache-Grant;
  unset bereq.http.X-Cache-Denied;
  unset bereq.http.X-Cache-Access-Check;
  unset bereq.http.X-Cache-Original-Url;
}

sub vcl_backend_response {
  if (bereq.uncacheable) {
    return (deliver);
  }

  if (bereq.http.X-Cache-Phase == "access_check") {
    # only cache the decision to grant access
    if (beresp.status != 200) {
      set beresp.uncacheable = true;
      set beresp.ttl = 120s;
      return (deliver);
    }
  } else if (beresp.status != 200
    || !beresp.http.X-Cache-Mode
    || (beresp.http.X-Cache-Mode == "shared" && !beresp.http.X-Cache-Security-Delegate)
    || (beresp.http.X-Cache-Mode == "user" && !bereq.http.X-Cache-User)) {
    # only cache successful responses, which state how they are cached
    set beresp.uncacheable = true;
    set beresp.ttl = 120s;
    return (deliver);
  }

  if (beresp.http.X-Cache-Mode == "user") {
    set beresp.http.Vary = beresp.http.Vary + ", X-Cache-User";
  }

  call vcl_beresp_stale;

  # Varnish by default disables caching whenever the reponse header "Set-Cookie" is set in the request (default safe behavior)
  # commenting the following line bypasses the default behaviour
  # call vcl_beresp_cookie;

  call vcl_beresp_control;
  call vcl_beresp_vary;

  # a grant must not be served after it expired
  if (bereq.http.X-Cache-Phase == "access_check") {
    set beresp.grace = 0s;
    set beresp.keep = 0s;
  }

  return (deliver);
}

sub vcl_deliver {
  # Continue the original request with the answer of the API
  if (req.http.X-Cache-Phase == "access_check") {
    if (resp.status == 200) {
      set req.http.X-Cache-Grant = req.http.X-Cache-Check-Iri;
    } else {
      set req.http.X-Cache-Denied = "true";
    }

    if (obj.hits > 0) {
      set req.http.X-Cache-Access-Check = "HIT";
    } else {
      set req.http.X-Cache-Access-Check = "MISS";
    }

    set req.url = req.http.X-Cache-Original-Url;
    unset req.http.X-Cache-Phase;
    unset req.http.X-Cache-Check-Iri;
    unset req.http.X-Cache-Original-Url;
    return (restart);
  }

  call fos_tags_xkey_deliver;
  call fos_debug_deliver;

  if (resp.http.X-Cache-Debug && req.http.X-Cache-Access-Check) {
    set resp.http.X-Cache-Access-Check = req.http.X-Cache-Access-Check;
  }
  unset resp.http.X-Cache-Mode;
  unset resp.http.X-Cache-Security-Delegate;

  # reset cache control header to avoid caching by any other upstream proxies
  if (resp.http.Content-Type ~ "application/hal\+json"){
    set resp.http.Cache-Control = "no-cache, private";
  }
}
