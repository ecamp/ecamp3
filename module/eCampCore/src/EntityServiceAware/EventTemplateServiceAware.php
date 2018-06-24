<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventTemplateServiceAware {
    /**
     * @return EntityService\EventTemplateService
     */
    public function getEventTemplateService();

    /**
     * @param EntityService\EventTemplateService $eventTemplateService
     */
    public function setEventTemplateService(EntityService\EventTemplateService $eventTemplateService);
}
