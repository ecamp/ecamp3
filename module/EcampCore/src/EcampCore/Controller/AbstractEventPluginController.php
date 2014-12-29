<?php

namespace EcampCore\Controller;

use EcampCore\Entity\Plugin;
use EcampLib\Controller\AbstractBaseController;

abstract class AbstractEventPluginController extends AbstractBaseController
{
    /**
     * @return \EcampCore\Repository\MediumRepository
     */
    protected function getMediumRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Medium');
    }

    /**
     * @return \EcampCore\Repository\PluginRepository
     */
    protected function getPluginRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Plugin');
    }

    /**
     * @return \EcampCore\Repository\EventRepository
     */
    protected function getEventRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Event');
    }

    /**
     * @return \EcampCore\Repository\EventPluginRepository
     */
    protected function getEventPluginRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\EventPlugin');
    }

    /**
     * @return \EcampCore\Plugin\StrategyProvider
     */
    protected function getStrategyProvider()
    {
        return $this->serviceLocator->get('EcampCore\Plugin\StrategyProvider');
    }

    /**
     * @param  Plugin                             $plugin
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    protected function getPluginStrategyInstance(Plugin $plugin)
    {
        return $this->getStrategyProvider()->Get($plugin);
    }

    /**
     * @return \EcampCore\Entity\Plugin
     */
    protected function getRoutePlugin()
    {
        $pluginId = $this->params('pluginId');

        if ($pluginId != null) {
            return $this->getPluginRepository()->find($pluginId);
        } else {
            $eventPlugin = $this->getRouteEventPlugin();
            if ($eventPlugin != null) {
                return $eventPlugin->getPlugin();
            }
        }

        return null;
    }

    /**
     * @return \EcampCore\Entity\Event
     */
    protected function getRouteEvent()
    {
        $eventId = $this->params('eventId');

        if ($eventId != null) {
            return $this->getEventRepository()->find($eventId);
        } else {
            $eventPlugin = $this->getRouteEventPlugin();
            if ($eventPlugin != null) {
                return $eventPlugin->getEvent();
            }
        }

        return null;
    }

    /**
     * @return \EcampCore\Entity\EventPlugin
     */
    protected function getRouteEventPlugin()
    {
        $eventPluginId = $this->params('eventPluginId');

        if ($eventPluginId != null) {
            return $this->getEventPluginRepository()->find($eventPluginId);
        }

        return null;
    }

}
