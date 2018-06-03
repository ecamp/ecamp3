<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;

class EventTemplateService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            EventTemplate::class,
            EventTemplateHydrator::class
        );
    }
}
