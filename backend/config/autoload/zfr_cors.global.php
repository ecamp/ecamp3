<?php
/**
 * This is the config file for ZfrCors.
 */
return [
    'zfr_cors' => [
        /**
         * Set the list of allowed origins domain with protocol.
         */
        'allowed_origins' => ['http://localhost:8888', 'http://ecamp3.dev', 'http://localhost:8080', 'http://localhost:3000', 'http://ecamp3.dev:8080'],
        /**
         * Set the list of HTTP verbs.
         */
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        /**
         * Set the list of headers. This is returned in the preflight request to indicate
         * which HTTP headers can be used when making the actual request
         */
        'allowed_headers' => ['Authorization', 'Accept', 'Content-Type'],
        /**
         * Set the max age of the preflight request in seconds. A non-zero max age means
         * that the preflight will be cached during this amount of time
         */
        // 'max_age' => 120,
        /**
         * Set the list of exposed headers. This is a whitelist that authorize the browser
         * to access to some headers using the getResponseHeader() JavaScript method. Please
         * note that this feature is buggy and some browsers do not implement it correctly
         */
        // 'exposed_headers' => [],
        /**
         * Standard CORS requests do not send or set any cookies by default. For this to work,
         * the client must set the XMLHttpRequest's "withCredentials" property to "true". For
         * this to work, you must set this option to true so that the server can serve
         * the proper response header.
         */
        'allowed_credentials' => true,
    ],
];
