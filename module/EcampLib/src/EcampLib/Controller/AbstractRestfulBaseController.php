<?php

namespace EcampLib\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Authentication\AuthenticationService;

abstract class AbstractRestfulBaseController extends AbstractRestfulController
{

    protected function me()
    {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            $userRepo = $this->getServiceLocator()->get('ecampcore.repo.user');

            return $userRepo->find($auth->getIdentity());
        }

        return null;
    }

}
