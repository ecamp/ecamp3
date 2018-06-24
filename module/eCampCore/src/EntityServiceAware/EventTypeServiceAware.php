<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventTypeServiceAware {
    /**
     * @return EntityService\EventTypeService
     */
    public function getEventTypeService();

    /**
     * @param EntityService\EventTypeService $eventTypeService
     */
    public function setEventTypeService(EntityService\EventTypeService $eventTypeService);
}
