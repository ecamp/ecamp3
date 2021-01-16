<?php

return
    eCampApi\V1\ConfigFactory::Create('ActivityCategory', 'ActivityCategories')
        ->addCollectionQueryWhitelist('campId')

        ->createInputFilterItem('short', true)
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->addValidatorStringLength(1, 16)
        ->buildInputFilter()

        ->createInputFilterItem('name', true)
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->addValidatorStringLength(1, 64)
        ->buildInputFilter()

        ->createInputFilterItem('color', true)
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->addValidatorStringLength(1, 8)
        ->addValidatorRegex('/#([a-f0-9]{3}){1,2}\b/i')
        ->buildInputFilter()

        ->createInputFilterItem('numberingStyle', true)
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->addValidatorStringLength(1, 1)
        ->buildInputFilter()

        ->addInputFilterItem('activityTypeId')

        ->buildConfig()
    ;
