<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;

class EventTypeService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            EventType::class,
            EventTypeHydrator::class
        );
    }
}
