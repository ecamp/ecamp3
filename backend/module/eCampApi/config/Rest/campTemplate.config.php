<?php

use eCampApi\V1\Factory\Config;

return Config::Create('CampTemplate')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
