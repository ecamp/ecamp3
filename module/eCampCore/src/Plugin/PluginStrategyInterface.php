<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

interface PluginStrategyInterface
{
    /**
     * @param EventPlugin $eventPlugin
     * @return array
     */
    function eventPluginExtract(EventPlugin $eventPlugin) : array;

    /**
     * @param EventPlugin $eventPlugin
     */
    function eventPluginCreated(EventPlugin $eventPlugin) : void;

}