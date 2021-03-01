<?php

use eCampApi\V1\Factory\Config;

return Config::Create('ContentType')
    ->setEntityHttpMethodsReadonly()
    ->setCollectionHttpMethodsReadonly()
    ->buildConfig()
;
