<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;
use eCamp\Lib\Service\ServiceUtils;

class MediumService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Medium::class,
            MediumHydrator::class
        );
    }
}
