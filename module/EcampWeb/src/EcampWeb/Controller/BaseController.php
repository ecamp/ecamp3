<?php

namespace EcampWeb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Response;
use EcampCore\Entity\User;
use EcampCore\Repository\MediumRepository;
use EcampCore\Entity\Medium;

abstract class BaseController
    extends AbstractActionController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function($e) { $this->setMeInViewModel($e); } , -100);

        $events->attach('dispatch', function($e) { $this->setConfigInViewModel($e); } , -100);
    }

    public function onDispatch( MvcEvent $e )
    {
        $this->getServiceLocator()->get('Twig_Environment')->getExtension('core')->setDateFormat('d.m.Y');

        parent::onDispatch($e);
    }

    /**
     * @param MvcEvent $e
     */
    private function setMeInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ViewModel) {
            $me = $this->getMe() ?: User::ROLE_GUEST;
            $acl = $this->getServiceLocator()->get('EcampCore\Acl');

            $result->setVariable('me', $me);
            $result->setVariable('acl', $acl);
        }
    }

     /**
     * @param MvcEvent $e
     */
    private function setConfigInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ViewModel) {
            $config = $this->getServiceLocator()->get('config');

            $result->setVariable('config', $config['ecamp']);
        }
    }

    /**
     * @return \EcampCore\Service\UserService
     */
    protected function getUserService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\User');
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getMe()
    {
        return $this->getUserService()->Get();
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getWebMedium()
    {
        $mediumRepository = $this->getServiceLocator()->get('EcampCore\Repository\Medium');

        return $mediumRepository->find(Medium::MEDIUM_WEB);
    }
    
    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getPrintMedium()
    {
    	$mediumRepository = $this->getServiceLocator()->get('EcampCore\Repository\Medium');
    
    	return $mediumRepository->find(Medium::MEDIUM_PRINT);
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

    protected function ajaxSuccssResponse($goToUrl = null)
    {
        $response = $this->getResponse();

        if ($goToUrl == null) {
            return $this->emptyResponse();
        } else {
            $response->getHeaders()->addHeaderLine('Location', $goToUrl);
            $response->setStatusCode(Response::STATUS_CODE_200);
        }

        return $response;
    }

}
