<?php

namespace EcampWeb\Controller\Profile;

use EcampLib\Validation\ValidationException;
use EcampWeb\Form\AjaxBaseForm;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
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
    }

    public function changePasswordAction()
    {
        /** @var AjaxBaseForm $form */
        $form = $this->createForm('EcampWeb\Form\Profile\ChangePasswordForm');
        $form->setAction(
            $this->url()->fromRoute('web/profile', array('action' => 'changePassword'))
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $login = $this->getLoginService()->Get();
                    $oldPassword = $data['check-password']['password'];
                    $newPassword = $data['set-password']['password1'];

                    try {
                        $this->getLoginService()->ChangePassword($login, $oldPassword, $newPassword);
                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('check-password', $e);
                    }

                    return $this->redirect()->toRoute('web/profile', array('action' => 'changePasswordSuccess'));

                } catch (ValidationException $e) {
                    $form->extractFromException(ValidationException::FromInnerException('data', $e));
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array(
            'form' => $form
        );
    }

    public function changePasswordSuccessAction()
    {
    }

    public function changeEmailAction()
    {
        /** @var AjaxBaseForm $form */
        $form = $this->createForm('EcampWeb\Form\Profile\ChangeEmailForm');
        $form->setAction(
            $this->url()->fromRoute('web/profile', array('action' => 'changeEmail'))
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $user = $this->getMe();
                    $email = $data['user-email']['email'];

                    try {
                        $this->getUserService()->UpdateEmail($user, $email);

                        return $this->redirect()->toRoute('web/profile', array('action' => 'changeEmailSuccess'));

                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('user-email', $e);
                    }

                } catch (ValidationException $e) {
                    $form->extractFromException(ValidationException::FromInnerException('data', $e));
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                }
            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array(
            'form' => $form
        );
    }

    public function changeEmailSuccessAction()
    {
    }

    public function verifyEmailAction()
    {
        $userId = $this->params()->fromQuery('user');
        $code = $this->params()->fromQuery('code');

        if (!$userId) {
            $response = $this->getResponse();
            //$response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        /** @var \EcampCore\Entity\User $user */
        $user = $this->getUserService()->Get($userId);

        if (!$user) {
            $response = $this->getResponse();
            //$response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        if (!$user->checkEmailVerificationCode($code)) {
            $response = $this->getResponse();
            //$response->setContent('');
            $response->setStatusCode(Response::STATUS_CODE_404);

            return $response;
        }

        $viewModel = new ViewModel(array(
            'user' => $user,
            'code' => $code
        ));

        try {
            $this->getUserService()->VerifyEmail($user, $code);
            $viewModel->setTemplate('ecamp-web/profile/index/verify-email-success');

        } catch (ValidationException $e) {
            $viewModel->setVariable('exception', $e);
            $viewModel->setTemplate('ecamp-web/profile/index/verify-email-failed');

        }

        return $viewModel;
    }

}
