<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Lib\Service\ServiceUtils;

class EventTemplateContainerService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventTemplateContainer::class,
            EventTemplateContainerHydrator::class
        );
    }
}
