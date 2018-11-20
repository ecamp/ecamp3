<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Plugin;
use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class PluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            Plugin::class,
            PluginHydrator::class
        );
    }
}
