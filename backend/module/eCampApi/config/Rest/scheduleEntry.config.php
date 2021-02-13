<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ScheduleEntry', 'ScheduleEntries')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('periodOffset', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('length', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilter('left')
    ->addInputFilter('width')
    ->buildConfig()
;
