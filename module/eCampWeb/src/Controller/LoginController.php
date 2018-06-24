<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Auth\AuthService;
use Zend\Http\Request;

class LoginController extends AbstractBaseController {
    /** @var AuthService */
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }


    public function indexAction() {
        $redirect = $this->params()->fromQuery('redirect');
        if (empty($redirect)) {
            $redirect = $this->url()->fromRoute('ecamp.web');
        }

        /** @var Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $username = $this->params()->fromPost('username');
            $password = $this->params()->fromPost('password');

            $result = $this->authService->login($username, $password);

            if ($result->isValid()) {
                return $this->redirect()->toUrl($redirect);
            } else {
                // TODO: Report error
            }
        }

        return ['redirect' => $redirect];
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('ecamp.web');
    }
}
