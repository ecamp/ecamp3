<?php

namespace EcampWeb\Controller\Profile;

use EcampLib\Validation\ValidationException;
use EcampWeb\Form\Profile\ChangePasswordForm;
use Zend\Http\PhpEnvironment\Response;

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
        $form = new ChangePasswordForm();
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

                    //$this->getResponse()->setStatusCode(Response::STATUS_CODE_204);
                    return $this->redirect()->toRoute('web/profile', array('action' => 'passwordChanged'));

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

    public function passwordChangedAction()
    {
    }

}
