<?php
/**
 * Created by PhpStorm.
 * User: pirminmattmann
 * Date: 07.05.14
 * Time: 21:58
 */

namespace EcampWeb\Controller\Auth;

use EcampLib\Service\ExecutionException;
use EcampLib\Validation\ValidationException;
use EcampWeb\Controller\BaseController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class RegisterController extends BaseController
{
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

                } catch (ValidationException $e) {
                    $form->setFormError($e->getMessage());
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (ExecutionException $e) {
                    $form->setFormError($e->getMessage());
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

}
