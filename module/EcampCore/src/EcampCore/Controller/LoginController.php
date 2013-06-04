<?php

namespace EcampCore\Controller;

use EcampCore\Auth\Bypass;
use Zend\Authentication\AuthenticationService;
use EcampLib\Controller\AbstractBaseController;

class LoginController extends AbstractBaseController
{

    public function bypassAction()
    {
        $id = $this->params()->fromQuery('id') ?: 1;
        $user = $this->getServiceLocator()->get('EcampCore\Repository\User')->find($id);

        $adapter = new Bypass($user);
        $auth = new AuthenticationService();

        $result = $auth->authenticate($adapter);
        die(var_dump($result->getCode()));
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        $auth->clearIdentity();

        die(!$auth->getIdentity() ? "logged out" : "");
    }

    public function checkAction()
    {
        $auth = new AuthenticationService();

        die($auth->getIdentity() ? "logged in" : "logged out");
    }

}
