<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Service\ServiceUtils;

class EventInstanceService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventInstance::class,
            EventInstanceHydrator::class
        );
    }
}
