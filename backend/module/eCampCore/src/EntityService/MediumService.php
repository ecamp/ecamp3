<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class MediumService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Medium::class,
            MediumHydrator::class,
            $authenticationService
        );
    }
}
