<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('MaterialItem')
    ->addCollectionQueryWhitelist('campId', 'materialListId', 'activityContentId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('article', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 64)
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('quantity')
            ->addValidatorIsFloat()
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('unit')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 64)
    )
    ->addInputFilter('materialListId')
    ->addInputFilter('periodId')
    ->addInputFilter('activityContentId')
    ->buildConfig()
;
