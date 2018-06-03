<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventTypePluginServiceAware
{
    /**
     * @return EntityService\EventTypePluginService
     */
    public function getEventTypePluginService();

    /**
     * @param EntityService\EventTypePluginService $eventTypePluginService
     */
    public function setEventTypePluginService(EntityService\EventTypePluginService $eventTypePluginService);
}
