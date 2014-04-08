<?php

namespace EcampWeb\Controller\Auth;

use EcampWeb\Form\Auth\RegisterForm;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Response;
use EcampCore\Entity\Autologin;
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

    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    public function loginAction()
    {
        $loginForm = new LoginForm();
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
                $autologinToken = $cookies[Autologin::COOKIE_NAME];

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
                    $headers->addHeader(new SetCookie(Autologin::COOKIE_NAME, $token, time() + Autologin::COOKIE_EXPIRES , '/'));
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
        $headers->addHeader(new SetCookie(Autologin::COOKIE_NAME, '', 0, '/'));

        return $result;
    }

    public function setPwAction()
    {
        $userId = $this->params()->fromQuery('userId');
        $password = $this->params()->fromQuery('password');

        /* @var $user \EcampCore\Entity\User */
        $user = $this->getUserRepository()->find($userId);
        $user->getLogin()->setNewPassword($password);

        return $this->emptyResponse();
    }

    public function checkUsernameAction()
    {
        $username = $this->params()->fromQuery('username');

        if (preg_match('/[^A-Za-z0-9_]/', $username)) {
            $resp = $this->emptyResponse(Response::STATUS_CODE_500);
            $resp->setContent("Allowed characters: a-z A-Z 0-9 _");

            return $resp;
        }

        if (strlen($username) >= 3) {
            $user = $this->getUserRepository()->findOneBy(array('username' => $username));

            if (empty($user)) {
                return $this->emptyResponse();
            } else {
                $resp = $this->emptyResponse(Response::STATUS_CODE_500);
                $resp->setContent("Username is already taken");

                return $resp;
            }
        } else {
            $resp = $this->emptyResponse(Response::STATUS_CODE_500);
            $resp->setContent("Minimum of username is 3 letters");

            return $resp;
        }

    }

}
