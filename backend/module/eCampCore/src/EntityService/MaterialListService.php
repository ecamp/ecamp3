<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\MaterialList;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Core\Hydrator\MaterialListHydrator;
use Laminas\Authentication\AuthenticationService;

class MaterialListService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialList::class,
            MaterialListHydrator::class,
            $authenticationService
        );
    }
}
