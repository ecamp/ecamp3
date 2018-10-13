<?php

namespace eCamp\Core\Plugin;

interface PluginStrategyProviderAware {
    /**
     * @return PluginStrategyProvider
     */
    public function getPluginStrategyProvider();

    /**
     * @param PluginStrategyProvider $pluginStrategyProvider
     */
    public function setPluginStrategyProvider(PluginStrategyProvider $pluginStrategyProvider);
}
