<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('CampCollaboration')
    ->addCollectionQueryWhitelist('campId', 'userId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('status')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('role', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('collaborationAcceptedBy')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->buildConfig()
;
