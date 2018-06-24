<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Service\RegisterService;
use Zend\Http\Request;

class RegisterController extends AbstractBaseController {

    /** @var RegisterService */
    private $registerService;


    public function __construct(RegisterService $registerService) {
        $this->registerService = $registerService;
    }


    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $username = $this->params()->fromPost('username');
            $mail = $this->params()->fromPost('mail');
            $password1 = $this->params()->fromPost('password1');
            $password2 = $this->params()->fromPost('password2');

            if ($password1 === $password2) {
                // Register
                $this->registerService->register($username, $mail, $password1);

                return $this->redirect()->toRoute('ecamp.web/login');
            }
        }

        return [];
    }
}
