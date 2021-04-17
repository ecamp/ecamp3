<?php

use eCampApi\V1\Factory\Config;

return Config::Create('Day')
    ->setEntityHttpMethods(['GET', 'PATCH'])
    ->setCollectionHttpMethodsReadonly()
    ->addCollectionQueryWhitelist('campId', 'periodId')
    ->buildConfig()
;
