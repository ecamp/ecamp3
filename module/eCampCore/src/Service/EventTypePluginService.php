<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Lib\Service\BaseService;

class EventTypePluginService extends BaseService
{
    public function __construct(EventTypePluginHydrator $eventTypePluginHydrator) {
        parent::__construct($eventTypePluginHydrator, EventTypePlugin::class);
    }
}
