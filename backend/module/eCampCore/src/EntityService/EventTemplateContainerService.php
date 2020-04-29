<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTemplateContainerService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventTemplateContainer::class,
            EventTemplateContainerHydrator::class,
            $authenticationService
        );
    }
}
