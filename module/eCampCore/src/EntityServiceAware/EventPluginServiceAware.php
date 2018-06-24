<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventPluginServiceAware {
    /**
     * @return EntityService\EventPluginService
     */
    public function getEventPluginService();

    /**
     * @param EntityService\EventPluginService $eventPluginService
     */
    public function setEventPluginService(EntityService\EventPluginService $eventPluginService);
}
