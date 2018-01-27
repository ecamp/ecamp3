<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

abstract class PluginStrategyBase implements PluginStrategyInterface
{
    /**
     * @param EventPlugin $eventPlugin
     * @return array
     */
    abstract function eventPluginExtract(EventPlugin $eventPlugin) : array;

    /**
     * @param EventPlugin $eventPlugin
     */
    abstract function eventPluginCreated(EventPlugin $eventPlugin) : void;
}
