<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait PluginServiceTrait
{
    /** @var EntityService\PluginService */
    private $pluginService;

    public function setPluginService(EntityService\PluginService $pluginService) {
        $this->pluginService = $pluginService;
    }

    public function getPluginService() {
        return $this->pluginService;
    }

}
