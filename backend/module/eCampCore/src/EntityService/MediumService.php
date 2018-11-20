<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Medium;
use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class MediumService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            Medium::class,
            MediumHydrator::class
        );
    }
}
