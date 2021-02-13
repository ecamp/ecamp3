<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('ActivityCategory', 'ActivityCategories')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilterFactry(
        InputFilterFactory::Create('short', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 16)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 64)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('color', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 8)
            ->addValidatorRegex('/#([a-f0-9]{3}){1,2}\b/i')
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('numberingStyle', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 1)
    )
    ->buildConfig()
;
