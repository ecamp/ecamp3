vcl 4.0;

import std;
import xkey;
import cookie;


backend default {
  .host = "caddy";
  .port = "3000";
}

# Hosts allowed to send BAN requests
acl purgers {
  "php";
}

sub vcl_recv {
  # Support xkey purge requests
  # see https://raw.githubusercontent.com/varnish/varnish-modules/master/src/vmod_xkey.vcc
  if (req.method == "PURGE") {
    if (client.ip !~ purgers) {
      return (synth(403, "Forbidden"));
    }
	  if (req.http.xkey) {
		  set req.http.n-gone = xkey.purge(req.http.xkey);
		  return (synth(200, "Invalidated "+req.http.n-gone+" objects"));
	  } else {
		  return (purge);
	  }
  }
  
  # exclude other services (frontend, print, etc.)
  if (req.url !~ "^/api") {
    return(pass);
  }

  # exclude API documentation, profiler and graphql endpoint
  if (req.url ~ "^/api/docs" 
    || req.url ~ "^/api/graphql" 
    || req.url ~ "^/api/bundles" 
    || req.url ~ "^/api/contexts" 
    || req.url ~ "^/api/_profiler" 
    || req.url ~ "^/api/_wdt") {
    return(pass);
  }

  # exclude any format other than HAL
  if (req.url !~ "\.jsonhal$" && req.http.Accept !~ "application/hal\+json"){
    return(pass);
  }
}

sub vcl_hash {
  if (req.http.Cookie) {
    # Include JWT cookies in cache hash 
    cookie.parse(req.http.Cookie);
    cookie.keep("localhost_jwt_hp,localhost_jwt_s");
    hash_data(cookie.get_string());
  }
}

sub vcl_req_cookie {
  # Varnish by default disables caching whenever the request header "Cookie" is set in the request (default safe behavior)
  # this bypasses the default behaviour; this is safe because we included "Cookie" in the "Vary" header
  return (hash);
}

sub vcl_beresp_cookie {
  # Varnish by default disables caching whenever the reponse header "Set-Cookie" is set in the request (default safe behavior)
  # this bypasses the default behaviour
	return (deliver);
}