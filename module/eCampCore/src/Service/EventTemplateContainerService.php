<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Lib\Service\BaseService;

class EventTemplateContainerService extends BaseService
{
    public function __construct(EventTemplateContainerHydrator $eventTemplateContainerHydrator)
    {
        parent::__construct($eventTemplateContainerHydrator, EventTemplateContainer::class);
    }
}
