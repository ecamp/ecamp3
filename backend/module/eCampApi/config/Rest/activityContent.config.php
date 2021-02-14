<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ActivityContent')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
