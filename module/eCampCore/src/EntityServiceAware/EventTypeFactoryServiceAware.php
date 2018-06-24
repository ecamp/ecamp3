<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventTypeFactoryServiceAware {
    /**
     * @return EntityService\EventTypeFactoryService
     */
    public function getEventTypeFactoryService();

    /**
     * @param EntityService\EventTypeFactoryService $eventTypeFactoryService
     */
    public function setEventTypeFactoryService(EntityService\EventTypeFactoryService $eventTypeFactoryService);
}
