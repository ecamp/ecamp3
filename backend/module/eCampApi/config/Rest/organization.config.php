<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('Organization')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
