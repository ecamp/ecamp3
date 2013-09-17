<?php

namespace EcampWeb\Controller\Camp;

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

            $camp = $this->getCampRepository()->find($controller->params('camp'));

            if(!$e->getResult() instanceof ViewModel)

                return;

            $e->getResult()->setVariable('camp',$camp);

            return;

        }, -100);
    }

    /**
     * @return \EcampCore\Service\CampService
     */
    protected function getCampService()
    {
        return $this->serviceLocator->get('EcampCore\Service\Camp');
    }

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    protected function getCampRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Camp');
    }

}
