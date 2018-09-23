<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Lib\Service\ServiceUtils;

class EventTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventTemplate::class,
            EventTemplateHydrator::class
        );
    }
}
