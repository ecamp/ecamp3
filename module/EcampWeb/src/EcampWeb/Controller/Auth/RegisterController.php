<?php

namespace EcampWeb\Controller\Auth;

use EcampLib\Validation\ValidationException;
use EcampWeb\Controller\BaseController;
use EcampWeb\Form\AjaxBaseForm;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class RegisterController extends BaseController
{
    /**
     * @return \EcampCore\Repository\LoginRepository
     */
    private function getLoginRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Login');
    }

    /**
     * @return \EcampCore\Service\RegisterService
     */
    private function getRegisterService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Register');
    }

    public function registerAction()
    {
        /** @var $form \EcampWeb\Form\Auth\RegisterForm */
        $form = $this->createForm('EcampWeb\Form\Auth\RegisterForm');
        $form->setAction($this->url()->fromRoute('web/register'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $user = $this->getRegisterService()->Register($data);

                    $viewModel = new ViewModel();
                    $viewModel->setTemplate('ecamp-web/auth/register/registered');
                    $viewModel->setVariable('user', $user);

                    return $viewModel;

                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                }
            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array('form' => $form);
    }

    public function activateAction()
    {
        $user = $this->params()->fromQuery('user');
        $code = $this->params()->fromQuery('code');

        if ($this->getRegisterService()->Activate($user, $code)) {
            $this->flashMessenger()->addSuccessMessage('You have successfully activated your eCamp account.');
        } else {
            $this->flashMessenger()->addErrorMessage('Activation failed');
        }

        $this->redirect()->toRoute('web/login');
    }

    public function forgotPasswordAction()
    {
        /** @var AjaxBaseForm $form */
        $form = $this->createForm('EcampWeb\Form\Auth\ForgotPasswordForm');
        $form->setAction($this->url()->fromRoute('web/register', array('action' => 'forgot-password')));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $userEmail = $data['user-email'];
                    try {
                        $email = $userEmail['email'];

                        echo "<b>";
                        echo $this->getRegisterService()->ForgotPassword($email);
                        echo "</b>";
                        die();

                        $viewModel = new ViewModel();
                        $viewModel->setTemplate('ecamp-web/auth/register/forgot-password-success');

                        return $viewModel;

                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('user-email', $ex);
                    }
                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                }
            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array('form' => $form);
    }

    public function resetPasswordAction()
    {
        $loginId = $this->params()->fromRoute('id');
        $resetKey = $this->params()->fromRoute('key');

        if (!$loginId) {
            $response = $this->getResponse();
            $response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        /** @var \EcampCore\Entity\Login $login */
        $login = $this->getLoginRepository()->find($loginId);

        if (!$login) {
            $response = $this->getResponse();
            $response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        if (!$login->checkPwResetKey($resetKey)) {
            $response = $this->getResponse();
            $response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        $form = $this->createForm('EcampWeb\Form\Auth\ResetPasswordForm');
        $form->setAction($this->url()->fromRoute('web/register',
            array('action' => 'resetPassword', 'id' => $loginId, 'key' => $resetKey)));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $setPassword = $data['set-password'];
                    try {
                        $password = $setPassword['password1'];
                        $this->getRegisterService()->ResetPassword($login, $resetKey, $password);

                        $viewModel = new ViewModel();
                        $viewModel->setTemplate('ecamp-web/auth/register/reset-password-success');

                        return $viewModel;

                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('set-password', $ex);
                    }

                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                }
            }
        }

        return array('form' => $form);
    }

}
