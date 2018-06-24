<?php

namespace eCamp\Core\Plugin;

trait PluginStrategyProviderTrait {
    /** @var PluginStrategyProvider */
    private $pluginStrategyProvider;

    public function setPluginStrategyProvider(PluginStrategyProvider $pluginStrategyProvider) {
        $this->pluginStrategyProvider = $pluginStrategyProvider;
    }

    public function getPluginStrategyProvider() {
        return $this->pluginStrategyProvider;
    }
}
