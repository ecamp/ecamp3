<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventPluginServiceTrait {
    /** @var EntityService\EventPluginService */
    private $eventPluginService;

    public function setEventPluginService(EntityService\EventPluginService $eventPluginService) {
        $this->eventPluginService = $eventPluginService;
    }

    public function getEventPluginService() {
        return $this->eventPluginService;
    }
}
