<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ContentNode')
    ->addCollectionQueryWhitelist('ownerId', 'parentId')
    ->setCollectionHttpMethods(['GET', 'POST', 'PATCH'])
    ->addInputFilterFactory(
        InputFilter::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('slot')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        Inputfilter::Create('position')
            ->addFilterDigits()
    )
    ->addInputFilter('jsonConfig')
    ->addInputFilter('parentId')
    ->buildConfig()
;
