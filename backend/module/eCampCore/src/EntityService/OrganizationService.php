<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Organization;
use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class OrganizationService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Organization::class,
            OrganizationHydrator::class,
            $authenticationService
        );
    }
}
