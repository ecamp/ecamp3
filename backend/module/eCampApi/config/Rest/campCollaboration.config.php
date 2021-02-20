<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('CampCollaboration')
    ->addCollectionQueryWhitelist('campId', 'userId')
    ->addInputFilterFactory(
        InputFilter::Create('status')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        InputFilter::Create('role', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        InputFilter::Create('collaborationAcceptedBy')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
