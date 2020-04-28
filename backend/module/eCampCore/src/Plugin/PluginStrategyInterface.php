<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

interface PluginStrategyInterface {
    /**
     * @return array
     */
    public function eventPluginExtract(EventPlugin $eventPlugin): array;

    public function eventPluginCreated(EventPlugin $eventPlugin): void;
}
