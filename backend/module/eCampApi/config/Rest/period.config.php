<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('Period')
    ->addCollectionQueryWhitelist('campId')
    ->addInputFilter('start', true)
    ->addInputFilter('end', true)
    ->addInputFilterFactory(
        InputFilterFactory::Create('description')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 128)
    )
    ->buildConfig()
;
