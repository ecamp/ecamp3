<?php

use eCampApi\V1\Factory\Config;
use eCampApi\V1\Factory\InputFilter;

return Config::Create('Period')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilter('start', true)
    ->addInputFilter('end', true)
    ->addInputFilterFactory(
        InputFilter::Create('description')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 128)
    )
    ->buildConfig()
;
