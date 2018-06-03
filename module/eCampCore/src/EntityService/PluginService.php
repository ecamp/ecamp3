<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Entity\Plugin;

class PluginService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            Plugin::class,
            PluginHydrator::class
        );
    }
}
