<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('MaterialItem')
    ->addCollectionQueryWhitelist('campId', 'materialListId', 'activityContentId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('article', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilter('quantity')
    ->addInputFilterFactry(
        InputFilterFactory::Create('unit')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilter('materialListId')
    ->addInputFilter('periodId')
    ->addInputFilter('activityContentId')
    ->buildConfig()
;
