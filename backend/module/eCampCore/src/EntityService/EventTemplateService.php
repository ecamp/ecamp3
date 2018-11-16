<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Lib\Service\ServiceUtils;

class EventTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            EventTemplate::class,
            EventTemplateHydrator::class
        );
    }
}
