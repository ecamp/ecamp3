<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\CampType;
use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class CampTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CampType::class,
            CampTypeHydrator::class,
            $authenticationService
        );
    }
}
