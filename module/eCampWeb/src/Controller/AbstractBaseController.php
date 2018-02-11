<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;

class AbstractBaseController extends \eCamp\Core\Controller\AbstractBaseController
{

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
