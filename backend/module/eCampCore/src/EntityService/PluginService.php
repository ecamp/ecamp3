<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Entity\Plugin;
use eCamp\Lib\Service\ServiceUtils;

class PluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            Plugin::class,
            PluginHydrator::class
        );
    }
}
