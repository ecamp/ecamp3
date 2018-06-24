<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;

class MediumService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            Medium::class,
            MediumHydrator::class
        );
    }
}
