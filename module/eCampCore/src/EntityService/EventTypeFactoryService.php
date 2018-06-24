<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Entity\EventTypeFactory;

class EventTypeFactoryService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            EventTypeFactory::class,
            EventTypeFactoryHydrator::class
        );
    }
}
