vcl 4.0;

import std;

backend default {
  .host = "caddy";
  .port = "3000";
}

sub vcl_recv {
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