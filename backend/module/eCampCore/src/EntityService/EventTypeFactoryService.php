<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Lib\Service\ServiceUtils;

class EventTypeFactoryService extends AbstractEntityService {

    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventTypeFactory::class,
            EventTypeFactoryHydrator::class
        );
    }
}
