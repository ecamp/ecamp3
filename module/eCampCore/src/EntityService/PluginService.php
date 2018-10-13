<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Entity\Plugin;
use eCamp\Lib\Service\ServiceUtils;

class PluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Plugin::class,
            PluginHydrator::class
        );
    }
}
