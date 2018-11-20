<?php

namespace eCamp\Api\Controller;

use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Auth\Adapter\LoginPassword;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Util\UrlUtils;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class LoginController extends AbstractActionController {
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

        $callback = $request->getQuery('callback');
        $token = $request->getQuery('token');
        if ($callback && $token) {
            // This request is made from an external client, redirect it to the requested url
            return $this->redirect()->toUrl(UrlUtils::addQueryParameterToUrl($callback, 'token', $token));
        }

        $data = [];

        /** @var User $user */
        $user = null;
        $userId = $this->authenticationService->getIdentity();
        if ($userId != null) {
            $user = $this->userService->fetch($userId);
        }
        if ($user != null) {
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

        $adapter = new LoginPassword($data->username, $data->password);
        $this->authenticationService->authenticate($adapter);

        return $this->redirect()->toRoute('ecamp.api/login');
    }

    /**
     * @return Response
     */
    public function logoutAction() {
        $this->authenticationService->clearIdentity();

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
