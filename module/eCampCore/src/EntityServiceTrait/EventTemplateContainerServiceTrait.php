<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventTemplateContainerServiceTrait
{
    /** @var EntityService\EventTemplateContainerService */
    private $eventTemplateContainerService;

    public function setEventTemplateContainerService(EntityService\EventTemplateContainerService $eventTemplateContainerService) {
        $this->eventTemplateContainerService = $eventTemplateContainerService;
    }

    public function getEventTemplateContainerService() {
        return $this->eventTemplateContainerService;
    }

}
