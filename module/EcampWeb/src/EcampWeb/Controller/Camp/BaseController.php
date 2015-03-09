<?php

namespace EcampWeb\Controller\Camp;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;
use EcampWeb\Controller\BaseController as WebBaseController;

abstract class BaseController
    extends WebBaseController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function ($e){ $this->setCampInViewModel($e); }, -100);
    }

    /**
     * @param MvcEvent $e
     */
    private function setCampInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();
        $controller = $e->getRouteMatch()->getParam('controller');
        $controller = substr($controller, 1 + strrpos($controller, '\\'));

        if ($result instanceof ViewModel) {
            $result->setVariable('camp', $this->getCamp());
            $result->setVariable('controller', $controller);
        }
    }

    /**
     * @return \EcampCore\Entity\Camp
     */
    protected function getCamp()
    {
        $campId = $this->params('camp');

        return $this->getCampRepository()->find($campId);
    }
}
