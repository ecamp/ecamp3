<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Entity\Plugin;
use eCamp\Lib\Service\BaseService;

class PluginService extends BaseService
{
    public function __construct(PluginHydrator $pluginHydrator)
    {
        parent::__construct($pluginHydrator, Plugin::class);
    }
}
