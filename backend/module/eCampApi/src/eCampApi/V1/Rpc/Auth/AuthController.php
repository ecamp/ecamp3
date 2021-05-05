<?php

namespace eCampApi\V1\Rpc\Auth;

use eCamp\Core\Auth\Adapter\LoginPassword;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hal\TemplatedLink;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController {
    private AuthenticationService $authenticationService;
    private UserService $userService;

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
        $user = $this->userService->findAuthenticatedUser();
        if (null != $user) {
            $data['user'] = $user;
            $data['role'] = $user->getRole();
        } else {
            $data['user'] = null;
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

        $data['google'] = TemplatedLink::factory([
            'rel' => 'google',
            'route' => [
                'name' => 'e-camp-api.rpc.auth.google',
            ],
        ]);

        $data['pbsmidata'] = TemplatedLink::factory([
            'rel' => 'pbsmidata',
            'route' => [
                'name' => 'e-camp-api.rpc.auth.pbsmidata',
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

    public function loginAction(): Response {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];
        $usernameOrEmail = isset($data->username) ? $data->username : '';
        $password = isset($data->password) ? $data->password : '';

        $user = $this->userService->findByUsername($usernameOrEmail);

        if (is_null($user)) {
            $user = $this->userService->findByTrustedMail($usernameOrEmail);
        }

        $adapter = new LoginPassword($user, $password);
        $this->authenticationService->authenticate($adapter);

        return $this->redirect()->toRoute('e-camp-api.rpc.auth');
    }

    public function googleAction(): Response {
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

    public function pbsMiDataAction(): Response {
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

    public function logoutAction(): Response {
        $this->authenticationService->clearIdentity();

        return $this->redirect()->toRoute('e-camp-api.rpc.auth');
    }
}
