<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('Activity', 'Activities')
    ->addCollectionQueryWhitelist('campId', 'periodId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('title', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
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
