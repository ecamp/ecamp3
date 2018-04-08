<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;
use eCamp\Lib\Service\BaseService;

class MediumService extends BaseService
{
    public function __construct(MediumHydrator $mediumHydrator) {
        parent::__construct($mediumHydrator, Medium::class);
    }
}
