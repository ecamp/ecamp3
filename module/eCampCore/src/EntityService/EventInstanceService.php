<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;

class EventInstanceService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            EventInstance::class,
            EventInstanceHydrator::class
        );
    }
}
