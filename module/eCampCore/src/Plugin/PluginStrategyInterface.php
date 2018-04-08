<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

interface PluginStrategyInterface
{
    /**
     * @param EventPlugin $eventPlugin
     * @return array
     */
    public function eventPluginExtract(EventPlugin $eventPlugin) : array;

    /**
     * @param EventPlugin $eventPlugin
     */
    public function eventPluginCreated(EventPlugin $eventPlugin) : void;
}
