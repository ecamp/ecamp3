<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventInstanceServiceAware {
    /**
     * @return EntityService\EventInstanceService
     */
    public function getEventInstanceService();

    /**
     * @param EntityService\EventInstanceService $eventInstanceService
     */
    public function setEventInstanceService(EntityService\EventInstanceService $eventInstanceService);
}
