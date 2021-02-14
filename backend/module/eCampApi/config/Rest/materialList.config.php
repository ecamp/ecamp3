<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('MaterialList')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilterFactory(
        InputFilterFactory::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->buildConfig()
;
