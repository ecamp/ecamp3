<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Auth\AuthService;

class LoginController extends AbstractBaseController
{
    /** @var AuthService */
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }


    public function indexAction() {
        $redirect = $this->params()->fromQuery('redirect');

        return [
            'redirect' => $redirect
        ];
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('ecamp.web');
    }

}
