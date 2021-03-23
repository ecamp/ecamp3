<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('Activity', 'Activities')
    ->addCollectionQueryWhitelist('campId', 'periodId')
    ->addInputFilterFactory(
        InputFilter::Create('title', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('location')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 128)
    )
    ->addInputFilter('campCollaborations')
    ->addInputFilter('scheduleEntries')
    ->addInputFilter('categoryId')
    ->buildConfig()
;
