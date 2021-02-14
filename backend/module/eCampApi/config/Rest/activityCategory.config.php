<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ActivityCategory', 'ActivityCategories')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('short', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 16)
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 64)
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('color', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 8)
            ->addValidatorRegex('/#([a-f0-9]{3}){1,2}\b/i')
    )
    ->addInputFilterFactory(
        InputFilterFactory::Create('numberingStyle', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 1)
    )
    ->buildConfig()
;
