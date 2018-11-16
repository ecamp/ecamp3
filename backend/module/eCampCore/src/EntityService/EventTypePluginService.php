<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Lib\Service\ServiceUtils;

class EventTypePluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventTypePlugin::class,
            EventTypePluginHydrator::class
        );
    }
}
