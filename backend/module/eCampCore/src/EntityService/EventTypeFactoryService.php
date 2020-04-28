<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTypeFactoryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventTypeFactory::class,
            EventTypeFactoryHydrator::class,
            $authenticationService
        );
    }
}
