<?php

namespace eCamp\Web\View;

use eCamp\Core\Auth\AuthService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AuthUserInjector extends AbstractListenerAggregate
{
    /** @var AuthService */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], -1000);
    }

    public function onDispatch(MvcEvent $e)
    {
        $res = $e->getResult();

        if ($res instanceof ViewModel) {
            $res->setVariable('authUser', $this->authService->getAuthUser());
        }
    }
}
