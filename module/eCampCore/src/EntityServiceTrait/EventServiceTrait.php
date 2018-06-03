<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventServiceTrait
{
    /** @var EntityService\EventService */
    private $eventService;

    public function setEventService(EntityService\EventService $eventService) {
        $this->eventService = $eventService;
    }

    public function getEventService() {
        return $this->eventService;
    }

}
