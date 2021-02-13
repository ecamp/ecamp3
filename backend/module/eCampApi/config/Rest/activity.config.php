<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('Activity', 'Activities')
    ->addCollectionQueryWhitelist('campId', 'periodId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('title', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('location')
            ->addFilterStringTrim()
            ->addFilterStripTags()
    )
    ->addInputFilter('progress')
    ->addInputFilter('campCollaborations')
    ->addInputFilter('scheduleEntries')
    ->addInputFilter('activityCategoryId')
    ->buildConfig()
;
