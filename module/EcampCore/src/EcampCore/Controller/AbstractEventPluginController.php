<?php

namespace EcampCore\Controller;

use EcampCore\Plugin\PluginBaseController;
use Zend\Http\PhpEnvironment\Response;
use EcampCore\Entity\Medium;
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

    abstract public function createAction();

    abstract public function getAction();

    public function deleteAction()
    {
        try {
            $eventPlugin = $this->getRouteEventPlugin();
            if ($eventPlugin == null) {
                throw new \Exception("EventPlugin does not exist");
            }

            $plugin = $eventPlugin->getPlugin();

            $pluginStrategy = $this->getPluginStrategyInstance($plugin);
            $pluginStrategy->delete($eventPlugin);

            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_200);

            return $response;

        } catch (\Exception $ex) {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_500);
            $response->setContent($ex->getMessage());

            return $response;
        }
    }

    /**
     * @param  \EcampCore\Entity\Plugin           $plugin
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    protected function getPluginStrategyInstance(Plugin $plugin)
    {
        $pluginStrategyClass = $plugin->getStrategyClass();
        $pluginStrategyFactory = $this->getServiceLocator()->get($pluginStrategyClass);

        return $pluginStrategyFactory->createStrategy();
    }

    /**
     * @param  \EcampCore\Entity\Event       $event
     * @param  \EcampCore\Entity\Plugin      $plugin
     * @return \EcampCore\Entity\EventPlugin
     */
    protected function createEventPlugin(Event $event, Plugin $plugin)
    {
        $pluginStrategy = $this->getPluginStrategyInstance($plugin);

        return $pluginStrategy->create($event, $plugin);
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

// class PluginController extends PluginBaseController
// {

// 	/**
// 	 * @return \EcampCore\Repository\MediumRepository
// 	 */
// 	protected function getMediumRepository(){
// 		return $this->getServiceLocator()->get('EcampCore\Repository\Medium');
// 	}

// 	/**
// 	 * @return \Doctrine\ORM\EntityRepository
// 	 */
// 	protected function getPluginRepository(){
// 		return $this->getServiceLocator()->get('EcampCore\Repository\Plugin');
// 	}

// 	/**
// 	 * @return \EcampCore\Repository\EventRepository
// 	 */
// 	protected function getEventRepository(){
// 		return $this->getServiceLocator()->get('EcampCore\Repository\Event');
// 	}

// 	/**
// 	 * @return \EcampCore\Service\EventPluginService
// 	 */
// 	protected function getEventPluginService(){
// 		return $this->getServiceLocator()->get('EcampCore\Service\EventPlugin');
// 	}

// 	/**
// 	 * @param \EcampCore\Entity\Plugin $plugin
// 	 * @return \EcampCore\Plugin\AbstractStrategy
// 	 */
// 	protected function getPluginStrategyInstance(Plugin $plugin){
// 		$pluginStrategyClass = $plugin->getStrategyClass();
// 		$pluginStrategyFactory = $this->getServiceLocator()->get($pluginStrategyClass);
// 		return $pluginStrategyFactory->createStrategy();
// 	}

// 	public function deleteAction(){
// 		$response = $this->getResponse();
// 		try {
// 			$eventPlugin = $this->getEventPlugin();

// 			// TODO: CHECK, IF PLUGIN CAN BE DELETED (MIN NUMBER)

// 			$pluginStrategy = $this->getPluginStrategyInstance($eventPlugin->getPlugin());
// 			$pluginStrategy->delete($eventPlugin);

// 			$response->setStatusCode(Response::STATUS_CODE_200);

// 		} catch (\Exception $e) {
// 			$response->setContent($e->getMessage());
// 			$response->setStatusCode(Response::STATUS_CODE_500);

// 		}
// 		return $response;
// 	}

// 	public function createAction(){
// 		$response = $this->getResponse();
// 		try {
// 			$eventId = $this->params()->fromRoute('eventId');
// 			$pluginId = $this->params()->fromRoute('pluginId');

// 			/* @var $event \EcampCore\Entity\Event */
// 			$event = $this->getEventRepository()->find($eventId);
// 			/* @var $plugin \EcampCore\Entity\Plugin */
// 			$plugin = $this->getPluginRepository()->find($pluginId);

// 			//TODO: CHECK, IF PLUGIN CAN BE CREATED (MAX NUMBER)

// 			$pluginStrategy = $this->getPluginStrategyInstance($plugin);
// 			$pluginStrategy->create($event, $plugin);

// 			$response->setStatusCode(Response::STATUS_CODE_200);

// 		} catch (\Exception $e) {
// 			$response->setContent($e->getMessage());
// 			$response->setStatusCode(Response::STATUS_CODE_500);

// 		}
// 		return $response;
// 	}

// }
