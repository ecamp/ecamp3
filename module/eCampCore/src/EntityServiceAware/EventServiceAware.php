<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventServiceAware {
    /**
     * @return EntityService\EventService
     */
    public function getEventService();

    /**
     * @param EntityService\EventService $eventService
     */
    public function setEventService(EntityService\EventService $eventService);
}
