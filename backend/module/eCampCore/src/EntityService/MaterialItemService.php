<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\MaterialItem;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Core\Hydrator\MaterialItemHydrator;
use Laminas\Authentication\AuthenticationService;

class MaterialItemService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialItem::class,
            MaterialItemHydrator::class,
            $authenticationService
        );
    }
}
