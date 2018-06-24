<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventTemplateContainerServiceAware {
    /**
     * @return EntityService\EventTemplateContainerService
     */
    public function getEventTemplateContainerService();

    /**
     * @param EntityService\EventTemplateContainerService $eventTemplateContainerService
     */
    public function setEventTemplateContainerService(EntityService\EventTemplateContainerService $eventTemplateContainerService);
}
