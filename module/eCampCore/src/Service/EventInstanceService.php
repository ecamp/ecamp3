<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Service\BaseService;

class EventInstanceService extends BaseService {
    public function __construct(EventInstanceHydrator $eventInstanceHydrator) {
        parent::__construct($eventInstanceHydrator, EventInstance::class);
    }
}
