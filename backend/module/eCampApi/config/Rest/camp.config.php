<?php

use eCampApi\V1\ConfigFactory;
use eCampApi\V1\InputFilterFactory;

return ConfigFactory::Create('Camp')
    ->addInputFilterFactry(
        InputFilterFactory::Create('name', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('title', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 64)
    )
    ->addInputFilterFactry(
        InputFilterFactory::Create('motto', true)
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 128)
    )
    ->buildConfig()
;
