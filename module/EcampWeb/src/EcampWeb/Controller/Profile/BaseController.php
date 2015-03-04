<?php

namespace EcampWeb\Controller\Profile;

use EcampWeb\Controller\BaseController as WebBaseController;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

abstract class BaseController
    extends WebBaseController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function($e) { $this->setUserInViewModel($e); } , -100);
    }

    /**
     * @param MvcEvent $e
     */
    private function setUserInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();
        $controller = $e->getRouteMatch()->getParam('controller');
        $controller = substr($controller, 1 + strrpos($controller, '\\'));

        if ($result instanceof ViewModel) {
            $result->setVariable('user', $this->getUser());
            $result->setVariable('controller', $controller);
        }
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getUser()
    {
        return $this->getUserService()->Get();
    }

}
