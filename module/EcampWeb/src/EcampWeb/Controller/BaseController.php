<?php

namespace EcampWeb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

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
        $this->getServiceLocator()->get('ZfcTwigEnvironment')->getExtension('core')->setDateFormat('d.m.Y');

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

            $result->setVariable('config', array('cdn'=>$config['ecamp']['cdn']));
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
}
