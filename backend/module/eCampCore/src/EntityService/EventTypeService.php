<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Service\ServiceUtils;

class EventTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventType::class,
            EventTypeHydrator::class
        );
    }
}
