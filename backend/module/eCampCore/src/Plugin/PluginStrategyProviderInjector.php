<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Core\Plugin\PluginStrategyProviderAware;

class PluginStrategyProviderInjector {

    /** @var PluginStrategyProvider */
    protected $pluginStrategyProvider;

    public function __construct(PluginStrategyProvider $pluginStrategyProvider) {
        $this->pluginStrategyProvider = $pluginStrategyProvider;
    }
 
    public function postLoad($eventArgs) {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof PluginStrategyProviderAware) {
            $entity->setPluginStrategyProvider($this->pluginStrategyProvider);
        }
    }
}
