<?php

use eCampApi\V1\Factory\Config;

return Config::Create('Organization')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
