<?php

return
    eCampApi\V1\ConfigFactory::Create('Activity', 'Activities')
        ->addCollectionQueryWhitelist('campId', 'periodId')

        ->createInputFilterItem('title', true)
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->addValidatorStringLength(1, 32)
        ->buildInputFilter()

        ->createInputFilterItem('location')
        ->addFilterStringTrim()
        ->addFilterStripTags()
        ->buildInputFilter()

        ->addInputFilterItem('progress')
        ->addInputFilterItem('campCollaborations')
        ->addInputFilterItem('scheduleEntries')
        ->addInputFilterItem('activityCategoryId')

        ->buildConfig()
;
