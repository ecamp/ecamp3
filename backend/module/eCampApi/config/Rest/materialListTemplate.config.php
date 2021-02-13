<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('MaterialListTemplate')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->addCollectionQueryWhitelist('campTemplateId')
    ->buildConfig()
;
