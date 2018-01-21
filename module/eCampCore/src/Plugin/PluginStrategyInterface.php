<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;

interface PluginStrategyInterface
{
    /**
     * @param EventPlugin $eventPlugin
     * @return array
     */
    function getHalLinks(EventPlugin $eventPlugin) : array;

    /**
     * @param EventPlugin $eventPlugin
     */
    function postCreated(EventPlugin $eventPlugin) : void;

}