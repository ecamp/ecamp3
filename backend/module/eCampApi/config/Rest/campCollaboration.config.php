<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('CampCollaboration')
    ->addCollectionQueryWhitelist('campId', 'userId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('status')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('role', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('collaborationAcceptedBy')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
