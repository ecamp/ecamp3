<?php

namespace EcampWeb\Controller\Group;

use Zend\EventManager\EventManagerInterface;

use Zend\View\Model\ViewModel;
use EcampWeb\Controller\BaseController as WebBaseController;

abstract class BaseController
    extends WebBaseController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {

            $group = $this->getGroupRepository()->find($controller->params('group'));

            if(!$e->getResult() instanceof ViewModel)

                return;

            $e->getResult()->setVariable('group',$group);

            return;

        }, -100);
    }

    /**
     * @return \EcampCore\Service\GroupService
     */
    protected function getGroupService()
    {
        return $this->serviceLocator->get('EcampCore\Service\Group');
    }

    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Group');
    }

}
