<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventCategoryServiceTrait
{
    /** @var EntityService\EventCategoryService */
    private $eventCategoryService;

    public function setEventCategoryService(EntityService\EventCategoryService $eventCategoryService) {
        $this->eventCategoryService = $eventCategoryService;
    }

    public function getEventCategoryService() {
        return $this->eventCategoryService;
    }

}
