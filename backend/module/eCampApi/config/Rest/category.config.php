<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('Category', 'Categories')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilterFactory(
        InputFilter::Create('short', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 16)
    )
    ->addInputFilterFactory(
        InputFilter::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('color', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 8)
            ->addValidatorRegex('/#([a-f0-9]{3}){1,2}\b/i')
    )
    ->addInputFilterFactory(
        InputFilter::Create('numberingStyle', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorInArray(['a', 'A', 'i', 'I', '1'])
    )
    ->buildConfig()
;
