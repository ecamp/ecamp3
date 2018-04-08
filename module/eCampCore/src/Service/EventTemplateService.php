<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Lib\Service\BaseService;

class EventTemplateService extends BaseService
{
    public function __construct(EventTemplateHydrator $eventTemplateHydrator) {
        parent::__construct($eventTemplateHydrator, EventTemplate::class);
    }
}
