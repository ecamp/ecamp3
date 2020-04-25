<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

abstract class PluginStrategyBase implements PluginStrategyInterface {
    /**
     * @return array
     */
    abstract public function eventPluginExtract(EventPlugin $eventPlugin): array;

    abstract public function eventPluginCreated(EventPlugin $eventPlugin): void;
}
