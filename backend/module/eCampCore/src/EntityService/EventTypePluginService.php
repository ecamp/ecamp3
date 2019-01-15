<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTypePluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventTypePlugin::class,
            EventTypePluginHydrator::class,
            $authenticationService
        );
    }
}
