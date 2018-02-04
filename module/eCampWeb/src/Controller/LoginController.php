<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Auth\AuthService;
use Zend\Http\Response;

class LoginController extends AbstractBaseController
{
    /** @var AuthService */
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }


    public function indexAction() {
        return [];
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('ecamp.web');
    }

    /**
     * @return Response
     */
    public function googleAction() {
        $redirect = $this->params()->fromRoute('url');
        if ($redirect == null) {
            $redirect = $this->url()->fromRoute('ecamp.web');
        }

        return $this->redirect()->toRoute(
            'ecamp.auth/google', [],
            [ 'query' => [ 'redirect' => $redirect ] ]
        );
    }

}
