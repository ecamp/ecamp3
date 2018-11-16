<?php

return [
    'jwt_zend_auth' => [
        // Choose signing method for the tokens
        'signer' => \Lcobucci\JWT\Signer\Hmac\Sha256::class,
        /*
            You need to specify either a signing key or set read only to true.
            If tokens are read only, the implementation will not automatically
            refresh tokens which are close to expiry so you will need to handle
            this yourself.
        */
        'readOnly' => false,
        // TODO read key from .env file or even better regenerate it regularly
        // Set the key to sign the token with, value is dependent on signer set.
        'signKey' => '93BD11612224292FB3246B6B34AD12BC6624FAC748E78B66F1CA46D5DD',
        // Set the key to verify the token with, value is dependent on signer set.
        'verifyKey' => '93BD11612224292FB3246B6B34AD12BC6624FAC748E78B66F1CA46D5DD',
        /*
            Default expiry for tokens. A token will expire after not being used
            for this number of seconds. A token which is used will automatically
            be extended provided a sign key is provided.
        */
        'expiry' => 600
    ]
];
