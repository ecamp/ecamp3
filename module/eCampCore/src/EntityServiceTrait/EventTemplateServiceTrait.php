<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait EventTemplateServiceTrait
{
    /** @var EntityService\EventTemplateService */
    private $eventTemplateService;

    public function setEventTemplateService(EntityService\EventTemplateService $eventTemplateService) {
        $this->eventTemplateService = $eventTemplateService;
    }

    public function getEventTemplateService() {
        return $this->eventTemplateService;
    }

}
