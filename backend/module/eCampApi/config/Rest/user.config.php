<?php

use eCamp\Core\Entity\User;
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
        InputFilter::Create('firstname')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('surname')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('nickname')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 32)
    )
    ->addInputFilterFactory(
        InputFilter::Create('role')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorInArray([
                User::ROLE_USER,
                User::ROLE_ADMIN,
            ])
    )
    ->addInputFilterFactory(
        InputFilter::Create('language')
            ->addFilterStringTrim()
            ->addFilterStripTags()
            ->addValidatorStringLength(1, 8)
    )
    ->buildConfig()
;
