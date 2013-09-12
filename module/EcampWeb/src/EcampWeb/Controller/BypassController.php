<?php

namespace EcampWeb\Controller;

use EcampLib\Controller\AbstractBaseController;
use EcampWeb\Form\User\BypassLoginForm;
use EcampCore\Auth\Bypass;
use Zend\Authentication\AuthenticationService;

class BypassController
    extends AbstractBaseController
{

    public function indexAction()
    {
        $orm = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $login = new BypassLoginForm($orm);
        $login->setAttribute('action', $this->url()->fromRoute(
            'dev/default',
            array(
                'controller' => 'bypass',
                'action'     => 'login',
            )
        ));

        return array(
            'login' => $login,
            'login_data' => $this->params()->fromQuery()
        );

    }

    public function loginAction()
    {
        $userRepo = $this->getServiceLocator()->get('EcampCore\Repository\User');
        $user = $userRepo->find($this->params()->fromQuery('user'));

        $ada = new Bypass($user);

        $authService = new AuthenticationService();
        $res = $authService->authenticate($ada);

        echo $res->isValid() ? 'logged id' : 'failed';
        die();
    }

}
