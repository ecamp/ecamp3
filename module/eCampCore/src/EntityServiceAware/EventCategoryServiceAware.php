<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface EventCategoryServiceAware {
    /**
     * @return EntityService\EventCategoryService
     */
    public function getEventCategoryService();

    /**
     * @param EntityService\EventCategoryService $eventCategoryService
     */
    public function setEventCategoryService(EntityService\EventCategoryService $eventCategoryService);
}
