<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Service\BaseService;

class EventTypeService extends BaseService {
    public function __construct(EventTypeHydrator $eventTypeHydrator) {
        parent::__construct($eventTypeHydrator, EventType::class);
    }
}
