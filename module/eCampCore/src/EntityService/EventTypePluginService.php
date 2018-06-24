<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Entity\EventTypePlugin;

class EventTypePluginService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            EventTypePlugin::class,
            EventTypePluginHydrator::class
        );
    }
}
