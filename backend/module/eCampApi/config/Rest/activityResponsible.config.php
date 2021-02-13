<?php

use eCampApi\V1\ConfigFactory;

return ConfigFactory::Create('ActivityResponsible')
    ->setEntityHttpMethods(['GET', 'DELETE'])
    ->addCollectionQueryWhitelist('activityId', 'campCollaborationId')
    ->buildConfig()
;
