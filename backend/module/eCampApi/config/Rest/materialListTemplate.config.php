<?php

use eCampApi\V1\Factory\Config;

return Config::Create('MaterialListTemplate')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->addCollectionQueryWhitelist('campTemplateId')
    ->buildConfig()
;
