<?php

use eCamp\Core\Entity\CampCollaboration;
use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('CampCollaboration')
    ->addCollectionQueryWhitelist('campId', 'userId')
    ->addInputFilterFactory(
        InputFilter::Create('status')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorInArray([
                CampCollaboration::STATUS_INVITED,
            ])
    )
    ->addInputFilterFactory(
        InputFilter::Create('role', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorInArray([
                CampCollaboration::ROLE_MEMBER,
                CampCollaboration::ROLE_MANAGER,
                CampCollaboration::ROLE_GUEST,
            ])
    )
    ->buildConfig()
;
