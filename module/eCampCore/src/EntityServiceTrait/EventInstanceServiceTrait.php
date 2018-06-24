<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventInstanceServiceTrait {
    /** @var EntityService\EventInstanceService */
    private $eventInstanceService;

    public function setEventInstanceService(EntityService\EventInstanceService $eventInstanceService) {
        $this->eventInstanceService = $eventInstanceService;
    }

    public function getEventInstanceService() {
        return $this->eventInstanceService;
    }
}
