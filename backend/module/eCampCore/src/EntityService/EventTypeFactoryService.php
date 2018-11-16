<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Lib\Service\ServiceUtils;

class EventTypeFactoryService extends AbstractEntityService {

    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventTypeFactory::class,
            EventTypeFactoryHydrator::class
        );
    }
}
