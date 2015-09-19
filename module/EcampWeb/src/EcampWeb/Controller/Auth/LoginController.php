<?php

namespace EcampWeb\Controller\Auth;

use EcampWeb\Form\Auth\RegisterForm;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Response;
use EcampCore\Entity\AutoLogin;
use EcampWeb\Controller\BaseController;
use EcampWeb\Form\Auth\LoginForm;

class LoginController extends BaseController
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
        /** @var \EcampWeb\Form\Auth\LoginForm $loginForm */
        $loginForm = $this->createForm('EcampWeb\Form\Auth\LoginForm');
        $loginForm->setAction($this->url()->fromRoute('web/login'));
        $loginForm->setRedirect($this->params()->fromQuery('redirect'));

        try {
            $authResult = false;
            $rememberMe = false;

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $loginForm->setData($data);

                $authResult = $this->getLoginService()->Login($data['login'], $data['password']);
                $rememberMe = $authResult->isValid() && $data['rememberme'];
            } elseif ($this->getRequest()->isGet()) {
                $cookies = $this->getRequest()->getCookie();
                $autologinToken = $cookies[AutoLogin::COOKIE_NAME];

                if ($autologinToken) {
                    $authResult = $this->getLoginService()->AutoLogin($autologinToken);
                }
            }

            if ($authResult && $authResult->isValid()) {

                $result = $loginForm->hasRedirect()
                ?   $this->redirect()->toUrl($loginForm->getRedirect())
                :   $this->redirect()->toRoute('web');

                if ($rememberMe) {
                    /* @var $user \EcampCore\Entity\User */
                    $user = $this->getUserRepository()->find($authResult->getIdentity());
                    $token = $this->getLoginService()->CreateAutoLoginToken($user);

                    $headers = $result->getHeaders();
                    $headers->addHeader(new SetCookie(AutoLogin::COOKIE_NAME, $token, time() + AutoLogin::COOKIE_EXPIRES , '/'));
                }

                return $result;
            }

        } catch (\Exception $ex) {
            return $this->emptyResponse(Response::STATUS_CODE_500);
        }

        return array(
            'login' => $loginForm,
            'register' => new RegisterForm()
        );
    }

    public function logoutAction()
    {
        $this->getLoginService()->Logout();
        $result = $this->redirect()->toRoute('web/login');

        $headers = $result->getHeaders();
        $headers->addHeader(new SetCookie(AutoLogin::COOKIE_NAME, '', 0, '/'));

        return $result;
    }

}
