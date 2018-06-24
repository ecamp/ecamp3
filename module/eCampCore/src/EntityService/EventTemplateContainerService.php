<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Entity\EventTemplateContainer;

class EventTemplateContainerService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            EventTemplateContainer::class,
            EventTemplateContainerHydrator::class
        );
    }
}
