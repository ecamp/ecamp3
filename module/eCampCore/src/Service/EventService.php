<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventHydrator;
use eCamp\Core\Entity\Event;
use eCamp\Lib\Service\BaseService;

class EventService extends BaseService
{
    public function __construct(EventHydrator $eventHydrator) {
        parent::__construct($eventHydrator, Event::class);
    }

}
