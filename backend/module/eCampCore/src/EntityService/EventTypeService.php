<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Service\ServiceUtils;

class EventTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventType::class,
            EventTypeHydrator::class
        );
    }
}
