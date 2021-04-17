<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('MaterialList')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilterFactory(
        InputFilter::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->buildConfig()
;
