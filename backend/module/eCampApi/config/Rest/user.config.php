<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('User')
    ->addCollectionQueryWhitelist('search')
    ->addInputFilterFactry(
        InputFilterFactory::Create('username')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('state', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 16)
    )
    ->buildConfig()
;
