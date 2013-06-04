<?php

namespace EcampLib\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;

abstract class AbstractBaseController extends AbstractActionController
{

    protected function me()
    {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            $userRepo = $this->getServiceLocator()->get('EcampCore\Repository\User');

            return $userRepo->find($auth->getIdentity());
        }

        return null;
    }

}
