<?php

use eCampApi\V1\Factory\Config;

return Config::Create('ActivityResponsible')
    ->setEntityHttpMethods(['GET', 'DELETE'])
    ->addCollectionQueryWhitelist('activityId', 'campCollaborationId')
    ->buildConfig()
;
