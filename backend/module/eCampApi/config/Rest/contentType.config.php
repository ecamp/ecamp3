<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('ContentType')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
