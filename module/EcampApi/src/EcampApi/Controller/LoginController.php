<?php

namespace EcampApi\Controller;

use EcampLib\Controller\AbstractBaseController;

class LoginController extends AbstractBaseController
{
    /**
     * @return \EcampCore\Service\LoginService
     */
    private function getLoginService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Login');
    }

    public function indexAction()
    {
        $this->redirect()->toRoute('api/default', array('controller' => 'login', 'action' => 'login'));
    }

    public function loginAction()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pw = $_SERVER['PHP_AUTH_PW'];

            $result = $this->getLoginService()->Login($user, $pw);

            if ($result->isValid()) {
                $this->redirect()->toRoute('api/rest', array('controller' => 'index'));
            }
        }

        header('WWW-Authenticate: Basic realm="eCamp V3 - Login"');
        header('HTTP/1.0 401 Unauthorized');

        echo 'Text to send if user hits Cancel button';
        exit;
    }

    public function logoutAction()
    {
        $result = $this->getLoginService()->Logout();
    }

}
