<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\EventType;
use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            EventType::class,
            EventTypeHydrator::class
        );
    }
}
