<?php
namespace eCampApi\V1\Rpc\Login;

use eCamp\Core\Auth\Adapter\LoginPassword;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
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
            'route' => [ 'name' => 'e-camp-api.rpc.index' ]
        ]);

        $data['native'] = Link::factory([
            'rel' => 'native',
            'route' => [
                'name' => 'e-camp-api.rpc.login',
                'params' => [ 'action' => 'login' ]
            ]
        ]);

        $data['google'] = Link::factory([
            'rel' => 'google',
            'route' => [
                'name' => 'e-camp-api.rpc.login',
                'params' => [ 'action' => 'google' ]
            ]
        ]);

        $data['pbsmidata'] = Link::factory([
          'rel' => 'pbsmidata',
          'route' => [
            'name' => 'e-camp-api.rpc.login',
            'params' => [ 'action' => 'pbsmidata' ]
          ]
        ]);

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.login'
        ]);

        if ($userId != null) {
            $data['logout'] = Link::factory([
                'rel' => 'logout',
                'route' => [
                    'name' => 'e-camp-api.rpc.login',
                    'params' => [ 'action' => 'logout' ]
                ]
            ]);
        }

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }


    public function loginAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = ($content != null) ? Json::decode($content) : [];
        $username = isset($data->username) ? $data->username : '';
        $password = isset($data->password) ? $data->password : '';

        $user = $this->userService->findByUsername($username);

        $adapter = new LoginPassword($user, $password);
        $this->authenticationService->authenticate($adapter);

        return $this->redirect()->toRoute('e-camp-api.rpc.login');
    }


    /**
     * @return Response
     */
    public function googleAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $externalCallback = $request->getQuery('callback');

        $redirect = $this->url()->fromRoute('e-camp-api.rpc.login', [], ['query'=>['callback'=>$externalCallback]]);

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

        $redirect = $this->url()->fromRoute('e-camp-api.rpc.login', [], [
            'query'=>['callback'=>$externalCallback]
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

        return $this->redirect()->toRoute('e-camp-api.rpc.login');
    }
}
