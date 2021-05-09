<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ScheduleEntry', 'ScheduleEntries')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilter('periodId')
    ->addInputFilterFactory(
        InputFilter::Create('periodOffset', true)
            ->addFilterDigits()
    )
    ->addInputFilterFactory(
        InputFilter::Create('length', true)
            ->addFilterDigits()
    )
    ->addInputFilter('left')
    ->addInputFilter('width')
    ->buildConfig()
;
