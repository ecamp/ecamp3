<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventTypeServiceTrait {
    /** @var EntityService\EventTypeService */
    private $eventTypeService;

    public function setEventTypeService(EntityService\EventTypeService $eventTypeService) {
        $this->eventTypeService = $eventTypeService;
    }

    public function getEventTypeService() {
        return $this->eventTypeService;
    }
}
