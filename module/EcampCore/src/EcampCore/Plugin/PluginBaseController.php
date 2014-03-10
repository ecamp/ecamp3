<?php

namespace EcampCore\Plugin;

use EcampLib\Controller\AbstractBaseController;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

abstract class PluginBaseController
    extends AbstractBaseController
{
    /**
     * @return \EcampCore\Repository\EventPluginRepository
     */
    public function getEventPluginRepo()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventPlugin');
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function($e) { $this->setEventPluginInViewModel($e); } , -100);
    }

    /**
     * @param MvcEvent $e
     */
    private function setEventPluginInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ViewModel) {
            $eventPluginId = $e->getRouteMatch()->getParam('eventPluginId');
            $eventPlugin = $this->getEventPluginRepo()->find($eventPluginId);
            // $this->getEventPlugin();
            $result->setVariable('eventPlugin', $eventPlugin);
        }
    }

    protected function getEventPlugin()
    {
    }

}
