<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ActivityContent')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
