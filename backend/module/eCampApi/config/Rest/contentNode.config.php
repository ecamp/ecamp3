<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ContentNode')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactory(
        InputFilter::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
