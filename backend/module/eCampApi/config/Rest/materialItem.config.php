<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('MaterialItem')
    ->addCollectionQueryWhitelist('campId', 'periodId', 'materialListId', 'contentNodeId')
    ->addInputFilterFactory(
        InputFilter::Create('article', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('quantity')
            ->addValidatorIsFloat()
    )
    ->addInputFilterFactory(
        InputFilter::Create('unit')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilter('materialListId')
    ->addInputFilter('periodId')
    ->addInputFilter('contentNodeId')
    ->buildConfig()
;
