vcl 4.0;

import std;
import cookie;
import var;
import digest;

include "./_config.vcl";
include "./fos_tags_xkey.vcl";
include "./fos_debug.vcl";
include "./purge_all.vcl";

sub vcl_recv {
  var.set("originalUrl", req.http.X-Forwarded-Prefix + req.url);

  if(var.get("originalUrl") ~ "^/api/varnish/healthcheck") {
    return(synth(200,"OK"));
  }

  call cache_reset_internal_headers;

  # Support xkey purge requests
  # see https://raw.githubusercontent.com/varnish/varnish-modules/master/src/vmod_xkey.vcc
  call fos_tags_xkey_recv;
  call purge_all_recv;

  # The access check request itself bypasses the exclusions, it is always an internal request
  if (req.http.X-Cache-Phase != "access_check") {
    call cache_exclude_uncacheable_requests;
  }

  # The user was denied access to the security delegate of the cached response, let the API answer
  if (req.http.X-Cache-Denied) {
    return(pass);
  }

  call cache_identify_user;
}

sub cache_reset_internal_headers {
  # SECURITY: headers used between restarts must never be taken over from a client
  if (req.restarts == 0) {
    unset req.http.X-Cache-Phase;
    unset req.http.X-Cache-Check-Delegate;
    unset req.http.X-Cache-Grant;
    unset req.http.X-Cache-Denied;
    unset req.http.X-Cache-Original-Url;
    unset req.http.X-Cache-Access-Check;
  }

  # SECURITY: derived from the login in cache_identify_user, never taken over from a client
  unset req.http.X-Cache-User;
}

sub cache_exclude_uncacheable_requests {
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

sub cache_identify_user {
  if (req.http.Cookie) {
    cookie.parse(req.http.Cookie);
    cookie.keep(std.getenv("COOKIE_PREFIX") + "jwt_hp," + std.getenv("COOKIE_PREFIX") + "jwt_s");

    if(cookie.get_string() == ""){
      return(pass); # Failsafe: Pass cache if JWT cookie is not set (also for example, if COOKIE_PREFIX is not properly configured)
    }

    # X-Cache-User separates cached grants and per-user responses by login
    set req.http.X-Cache-User = digest.hash_sha256(cookie.get_string());
  }
}

sub vcl_hit {
  if (req.http.X-Cache-Phase == "access_check") {
    return(deliver);
  }

  # A response cached per user is only found by the user it was fetched for
  if (obj.http.X-Cache-Mode == "public" || obj.http.X-Cache-Mode == "user") {
    return(deliver);
  }

  # SECURITY: never serve a response that doesn't state who may read it
  if (obj.http.X-Cache-Mode != "shared" || !obj.http.X-Cache-Security-Delegate) {
    return(pass);
  }

  if (req.http.X-Cache-Grant && req.http.X-Cache-Grant == obj.http.X-Cache-Security-Delegate) {
    return(deliver);
  }

  call access_check_start;
}

sub access_check_start {
  # Ask the API whether the user may read the security delegate, and continue with the original request afterwards
  set req.http.X-Cache-Phase = "access_check";
  set req.http.X-Cache-Check-Delegate = obj.http.X-Cache-Security-Delegate;
  set req.http.X-Cache-Original-Url = req.url;
  set req.url = "/authorization/check_access?iri=" + obj.http.X-Cache-Security-Delegate;
  return(restart);
}

sub access_check_finish {
  if (resp.status == 200) {
    set req.http.X-Cache-Grant = req.http.X-Cache-Check-Delegate;
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
  unset req.http.X-Cache-Check-Delegate;
  unset req.http.X-Cache-Original-Url;
  return (restart);
}

sub vcl_req_cookie {
  # Varnish by default disables caching whenever the request header "Cookie" is set
  # Safe to bypass: cached responses are only delivered after vcl_hit checked that the user may read them
  return (hash);
}

sub vcl_backend_fetch {
  unset bereq.http.X-Cache-Check-Delegate;
  unset bereq.http.X-Cache-Grant;
  unset bereq.http.X-Cache-Denied;
  unset bereq.http.X-Cache-Access-Check;
  unset bereq.http.X-Cache-Original-Url;
}

sub vcl_backend_response {
  if (bereq.uncacheable) {
    return (deliver);
  }

  call cache_decide_cacheability;

  if (beresp.http.X-Cache-Mode == "user" || bereq.http.X-Cache-Phase == "access_check") {
    call cache_vary_by_user;
  }

  call vcl_beresp_stale;
  call vcl_beresp_control;
  call vcl_beresp_vary;

  # a grant must not be served after it expired
  if (bereq.http.X-Cache-Phase == "access_check") {
    set beresp.grace = 0s;
    set beresp.keep = 0s;
  }

  return (deliver);
}

sub cache_decide_cacheability {
  if (beresp.status != 200) {
    # only cache successful responses
    call cache_uncacheable;
  } else if (bereq.http.X-Cache-Phase == "access_check") {
    # a grant is cached per login
    if (!bereq.http.X-Cache-User) {
      call cache_uncacheable;
    }
  } else if (!beresp.http.X-Cache-Mode) {
    # the API did not declare how to cache
    call cache_uncacheable;
  } else if (beresp.http.X-Cache-Mode == "shared" && !beresp.http.X-Cache-Security-Delegate) {
    call cache_uncacheable;
  } else if (beresp.http.X-Cache-Mode == "user" && !bereq.http.X-Cache-User) {
    call cache_uncacheable;
  }
}

sub cache_uncacheable {
  set beresp.uncacheable = true;
  set beresp.ttl = 120s;
  return (deliver);
}

sub cache_vary_by_user {
  if (beresp.http.Vary) {
    set beresp.http.Vary = beresp.http.Vary + ", X-Cache-User";
  } else {
    set beresp.http.Vary = "X-Cache-User";
  }
}

sub vcl_deliver {
  # Continue the original request with the answer of the API
  if (req.http.X-Cache-Phase == "access_check") {
    call access_check_finish;
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
