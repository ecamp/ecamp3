<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Service\ServiceUtils;

class EventInstanceService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventInstance::class,
            EventInstanceHydrator::class
        );
    }
}
