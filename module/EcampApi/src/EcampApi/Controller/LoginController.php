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

    public function loginAction()
    {
        $user = $this->params()->fromPost('user') ?: $_SERVER['PHP_AUTH_USER'];
        $pw = $this->params()->fromPost('password') ?: $_SERVER['PHP_AUTH_PW'];

        if (isset($user) && isset($pw)) {
            $result = $this->getLoginService()->Login($user, $pw);

            if ($result->isValid()) {
                $this->redirect()->toRoute('api/rest', array('controller' => 'index'));
            } else {
                $response = $this->getResponse();
                $response->setStatusCode(401);
                $response->sendHeaders();

                exit;
            }
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('WWW-Authenticate', 'Basic realm="eCamp V3 - Login"');
        $response->setStatusCode(401);
        $response->sendHeaders();

        exit;
    }

    public function logoutAction()
    {
        $result = $this->getLoginService()->Logout();

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->sendHeaders();

        exit;
    }

}
