<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AbstractBaseController extends \eCamp\Core\Controller\AbstractBaseController
{

    public function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setViewModelTerminal'], -90);
    }

    public function setViewModelTerminal(MvcEvent $e) {
        $vm = $e->getResult();

        if ($vm instanceof ViewModel) {
            $vm->setTerminal(true);
        }
    }


    public function onDispatch(MvcEvent $e) {
        try {
            $result = parent::onDispatch($e);
        } catch (AuthRequiredException $ex) {
            /** @var Request $req */
            $req = $this->getRequest();
            $url = $req->getRequestUri();

            return $this->redirect()->toRoute(
                'ecamp.web/login', [], [ 'query' => [ 'redirect' => $url ] ]
            );
        }

        return $result;
    }

    /**
     * @throws AuthRequiredException
     */
    public function forceLogin() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            throw new AuthRequiredException();
        }
    }

}
