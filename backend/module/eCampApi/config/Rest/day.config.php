<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('Day')
    ->setEntityHttpMethods(['GET', 'PATCH'])
    ->setCollectionHttpMethodsReadonly()
    ->addCollectionQueryWhitelist('campId', 'periodId')
    ->buildConfig()
;
