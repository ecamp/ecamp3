<?php

namespace eCampApi\V1\Rpc\Auth;

use eCamp\Core\Auth\Adapter\LoginPassword;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController {
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var UserService */
    private $userService;

    public function __construct(
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
    }

    /**
     * @return HalJsonModel|Response
     */
    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            return $this->loginAction();
        }

        $callback = $request->getQuery('callback');
        if ($callback) {
            // This request is made from an external client, redirect it to the requested url
            return $this->redirect()->toUrl($callback);
        }

        $data = [];

        /** @var User $user */
        $user = $this->userService->findAuthenticatedUser();
        if (null != $user) {
            $data['user'] = $user->getDisplayName();
            $data['username'] = $user->getUsername();
            $data['role'] = $user->getRole();
        } else {
            $data['user'] = 'guest';
            $data['username'] = 'guest';
            $data['role'] = 'guest';
        }

        $data['api'] = Link::factory([
            'rel' => 'api',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'api',
                ],
            ],
        ]);

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.auth',
        ]);

        $data['register'] = Link::factory([
            'rel' => 'register',
            'route' => [
                'name' => 'e-camp-api.rpc.register',
                'params' => ['action' => 'register'],
            ],
        ]);

        $data['login'] = Link::factory([
            'rel' => 'login',
            'route' => [
                'name' => 'e-camp-api.rpc.auth',
                'params' => ['action' => 'login'],
            ],
        ]);

        $data['google'] = Link::factory([
            'rel' => 'google',
            'route' => [
                'name' => 'e-camp-api.rpc.auth',
                'params' => ['action' => 'google'],
            ],
        ]);

        $data['pbsmidata'] = Link::factory([
            'rel' => 'pbsmidata',
            'route' => [
                'name' => 'e-camp-api.rpc.auth',
                'params' => ['action' => 'pbsmidata'],
            ],
        ]);

        $data['logout'] = Link::factory([
            'rel' => 'logout',
            'route' => [
                'name' => 'e-camp-api.rpc.auth',
                'params' => ['action' => 'logout'],
            ],
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }

    public function loginAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];
        $username = isset($data->username) ? $data->username : '';
        $password = isset($data->password) ? $data->password : '';

        $user = $this->userService->findByUsername($username);

        $adapter = new LoginPassword($user, $password);
        $this->authenticationService->authenticate($adapter);

        return $this->redirect()->toRoute('e-camp-api.rpc.auth');
    }

    /**
     * @return Response
     */
    public function googleAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $externalCallback = $request->getQuery('callback');

        $redirect = $this->url()->fromRoute('e-camp-api.rpc.auth', [], ['query' => ['callback' => $externalCallback]]);

        return $this->redirect()->toRoute(
            'ecamp.auth/google',
            [],
            ['query' => ['redirect' => $redirect]]
        );
    }

    /**
     * @return Response
     */
    public function pbsMiDataAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $externalCallback = $request->getQuery('callback');

        $redirect = $this->url()->fromRoute('e-camp-api.rpc.auth', [], [
            'query' => ['callback' => $externalCallback],
        ]);

        return $this->redirect()->toRoute(
            'ecamp.auth/pbsmidata',
            [],
            ['query' => ['redirect' => $redirect]]
        );
    }

    /**
     * @return Response
     */
    public function logoutAction() {
        $this->authenticationService->clearIdentity();

        return $this->redirect()->toRoute('e-camp-api.rpc.auth');
    }
}
