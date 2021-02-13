<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('CampTemplate')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
