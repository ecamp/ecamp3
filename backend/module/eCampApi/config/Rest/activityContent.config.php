<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ActivityContent')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactory(
        InputFilter::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
