<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ActivityTypeFactory;
use eCamp\Core\Hydrator\ActivityTypeFactoryHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityTypeFactoryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityTypeFactory::class,
            ActivityTypeFactoryHydrator::class,
            $authenticationService
        );
    }
}
