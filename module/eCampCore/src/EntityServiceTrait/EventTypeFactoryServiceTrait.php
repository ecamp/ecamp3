<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventTypeFactoryServiceTrait {
    /** @var EntityService\EventTypeFactoryService */
    private $eventTypeFactoryService;

    public function setEventTypeFactoryService(EntityService\EventTypeFactoryService $eventTypeFactoryService) {
        $this->eventTypeFactoryService = $eventTypeFactoryService;
    }

    public function getEventTypeFactoryService() {
        return $this->eventTypeFactoryService;
    }
}
