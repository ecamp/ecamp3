<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventTypePluginServiceTrait {
    /** @var EntityService\EventTypePluginService */
    private $eventTypePluginService;

    public function setEventTypePluginService(EntityService\EventTypePluginService $eventTypePluginService) {
        $this->eventTypePluginService = $eventTypePluginService;
    }

    public function getEventTypePluginService() {
        return $this->eventTypePluginService;
    }
}
