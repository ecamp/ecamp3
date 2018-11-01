<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Lib\Service\ServiceUtils;

class EventTemplateContainerService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventTemplateContainer::class,
            EventTemplateContainerHydrator::class
        );
    }
}
