<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ContentNode')
    ->addCollectionQueryWhitelist('ownerId', 'parentId')
    ->addInputFilterFactory(
        InputFilter::Create('instanceName')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        InputFilter::Create('slot')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilterFactory(
        Inputfilter::Create('position')
            ->addValidatorIsFloat()
    )
    ->addInputFilter('jsonConfig')
    ->buildConfig()
;
