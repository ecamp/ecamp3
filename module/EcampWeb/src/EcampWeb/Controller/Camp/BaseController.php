<?php

namespace EcampWeb\Controller\Camp;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;
use EcampWeb\Controller\BaseController as WebBaseController;
use Zend\Http\Response;

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

    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\User');
    }

    /**
     * @param  integer                        $statusCode
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function emptyResponse($statusCode = Response::STATUS_CODE_200)
    {
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        return $response;
    }
}
