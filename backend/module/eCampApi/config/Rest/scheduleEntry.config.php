<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ScheduleEntry', 'ScheduleEntries')
    ->addCollectionQueryWhitelist('activityId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('periodOffset', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('length', true)
            ->addFilterStripTags()
            ->addFilter(\Laminas\Filter\Digits::class)
    )
    ->addInputFilter('left')
    ->addInputFilter('width')
    ->buildConfig()
;
