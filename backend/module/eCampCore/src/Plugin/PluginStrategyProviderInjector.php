<?php

namespace eCamp\Core\Plugin;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class PluginStrategyProviderInjector {
    /** @var PluginStrategyProvider */
    protected $pluginStrategyProvider;

    public function __construct(PluginStrategyProvider $pluginStrategyProvider) {
        $this->pluginStrategyProvider = $pluginStrategyProvider;
    }

    public function postLoad(LifecycleEventArgs $eventArgs) {
        $this->inject($eventArgs);
    }

    public function prePersist(LifecycleEventArgs $eventArgs) {
        $this->inject($eventArgs);
    }

    private function inject(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof PluginStrategyProviderAware) {
            $entity->setPluginStrategyProvider($this->pluginStrategyProvider);
        }
    }
}
