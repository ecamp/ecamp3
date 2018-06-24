<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Lib\Service\BaseService;

class EventTypeFactoryService extends BaseService {
    public function __construct(EventTypeFactoryHydrator $eventTypeFactoryHydrator) {
        parent::__construct($eventTypeFactoryHydrator, EventTypeFactory::class);
    }
}
