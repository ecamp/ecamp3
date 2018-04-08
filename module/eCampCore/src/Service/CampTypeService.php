<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Entity\CampType;
use eCamp\Lib\Service\BaseService;

class CampTypeService extends BaseService
{
    public function __construct(CampTypeHydrator $campTypeHydrator)
    {
        parent::__construct($campTypeHydrator, CampType::class);
    }
}
