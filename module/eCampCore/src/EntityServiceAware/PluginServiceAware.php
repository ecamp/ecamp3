<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface PluginServiceAware {
    /**
     * @return EntityService\PluginService
     */
    public function getPluginService();

    /**
     * @param EntityService\PluginService $pluginService
     */
    public function setPluginService(EntityService\PluginService $pluginService);
}
