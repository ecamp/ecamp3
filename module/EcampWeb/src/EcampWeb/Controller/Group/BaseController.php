<?php

namespace EcampWeb\Controller\Group;

use Zend\EventManager\EventManagerInterface;

use Zend\View\Model\ViewModel;
use EcampWeb\Controller\BaseController as WebBaseController;
use Zend\Mvc\MvcEvent;

abstract class BaseController
    extends WebBaseController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function($e) { $this->setGroupInViewModel($e); } , -100);
    }

    /**
     * @param MvcEvent $e
     */
    private function setGroupInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();
        $controller = $e->getRouteMatch()->getParam('controller');
        $controller = substr($controller, 1 + strrpos($controller, '\\'));

        if ($result instanceof ViewModel) {
            $result->setVariable('group', $this->getGroup());
            $result->setVariable('controller', $controller);
        }
    }

    /**
     * @return \EcampCore\Entity\Group
     */
    protected function getGroup()
    {
        $groupId = $this->params('group');

        return $this->getGroupRepository()->find($groupId);
    }

    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Group');
    }

    /**
     * @return \EcampCore\Service\GroupService
     */
    protected function getGroupService()
    {
        return $this->serviceLocator->get('EcampCore\Service\Group');
    }

}
