<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('User')
    ->addCollectionQueryWhitelist('search')
    ->addInputFilterFactory(
        InputFilter::Create('username')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('state', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 16)
    )
    ->buildConfig()
;
