<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

abstract class PluginStrategyBase implements PluginStrategyInterface {
    abstract public function eventPluginExtract(EventPlugin $eventPlugin): array;

    abstract public function eventPluginCreated(EventPlugin $eventPlugin): void;
}
