<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('ScheduleEntry', 'ScheduleEntries')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactory(
        InputFilter::Create('periodOffset', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilterFactory(
        InputFilter::Create('length', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilter('left')
    ->addInputFilter('width')
    ->buildConfig()
;
