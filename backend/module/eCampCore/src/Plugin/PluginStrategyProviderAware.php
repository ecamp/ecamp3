<?php

namespace eCamp\Core\Plugin;

interface PluginStrategyProviderAware {
    /**
     * @return PluginStrategyProvider
     */
    public function getPluginStrategyProvider();

    public function setPluginStrategyProvider(PluginStrategyProvider $pluginStrategyProvider);
}
