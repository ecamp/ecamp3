<?php

namespace eCamp\Api\Controller;

use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Auth\AuthService;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Acl\NoAccessException;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class LoginController extends AbstractActionController {
    /** @var AuthService */
    private $authService;

    /** @var UserService */
    private $userService;


    public function __construct(
        AuthService $authService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }


    /**
     * @return Response|HalJsonModel
     * @throws NoAccessException
     * @throws NonUniqueResultException
     */
    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            return $this->loginAction();
        }


        $data = [];

        $user = null;
        $userId = $this->authService->getAuthUserId();
        if ($userId != null) {
            $user = $this->userService->fetch($userId);
        }
        if ($user != null) {

            $callback = $request->getQuery('callback');
            if ($callback) {
                // This request is made from an external client, redirect it to the requested url
                return $this->redirect()->toUrl($callback);
            }

            $data['user'] = $user->getDisplayName();
            $data['role'] = $user->getRole();
        } else {
            $data['user'] = 'guest';
            $data['role'] = 'guest';
        }

        $data['home'] = Link::factory([
                'rel' => 'home',
                'route' => [ 'name' => 'ecamp.api' ]
            ]);

        $data['google'] = Link::factory([
                'rel' => 'google',
                'route' => [
                    'name' => 'ecamp.api/login',
                    'params' => [ 'action' => 'google' ]
                ]
            ]);

        if ($userId != null) {
            $data['logout'] = Link::factory([
                'rel' => 'logout',
                'route' => ['name' => 'ecamp.api/logout']
            ]);
        }

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }

    /**
     * @return Response
     * @throws NonUniqueResultException
     */
    public function loginAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = ($content != null) ? Json::decode($content) : [];
        $this->authService->login($data->username, $data->password);

        return $this->redirect()->toRoute('ecamp.api/login');
    }

    /**
     * @return Response
     */
    public function logoutAction() {
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('ecamp.api/login');
    }

    /**
     * @return Response
     */
    public function googleAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $externalCallback = $request->getQuery('callback');

        $redirect = $this->url()->fromRoute('ecamp.api/login', [], ['query'=>['callback'=>$externalCallback]]);

        return $this->redirect()->toRoute(
            'ecamp.auth/google',
            [],
            ['query' => ['redirect' => $redirect]]
        );
    }
}
