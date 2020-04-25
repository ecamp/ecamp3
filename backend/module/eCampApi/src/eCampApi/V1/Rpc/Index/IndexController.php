<?php

namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hal\TemplatedLink;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class IndexController extends AbstractActionController {
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

    public function indexAction() {
        $data = [];
        $data['title'] = 'eCamp V3';

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.index',
        ]);

        $data['api'] = Link::factory([
            'rel' => 'api',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'api',
                ],
            ],
        ]);

        $data['setup'] = Link::factory([
            'rel' => 'setup',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'setup.php',
                ],
            ],
        ]);

        $data['php-info'] = Link::factory([
            'rel' => 'php-info',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'info.php',
                ],
            ],
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }

    public function apiAction() {
        $data = [];
        $data['title'] = 'eCamp V3 - API';

        /** @var User $user */
        $user = null;
        $userId = $this->authenticationService->getIdentity();
        if (null != $userId) {
            $user = $this->userService->fetch($userId);
        }
        if (null != $user) {
            $data['user'] = $user->getDisplayName();

            $data['profile'] = Link::factory([
                'rel' => 'profile',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.user',
                    'params' => ['user_id' => $userId],
                ],
            ]);
        } else {
            $data['user'] = 'guest';
        }

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'api',
                ],
            ],
        ]);

        $data['auth'] = Link::factory([
            'rel' => 'auth',
            'route' => 'e-camp-api.rpc.auth',
        ]);

        $data['docu'] = Link::factory([
            'rel' => 'docu',
            'route' => 'zf-apigility/swagger',
        ]);

        $data['admin'] = Link::factory([
            'rel' => 'admin',
            'route' => 'zf-apigility/ui',
        ]);

        $data['users'] = TemplatedLink::factory([
            'rel' => 'users',
            'route' => 'e-camp-api.rest.doctrine.user',
        ]);

        $data['camps'] = TemplatedLink::factory([
            'rel' => 'camps',
            'route' => 'e-camp-api.rest.doctrine.camp',
        ]);

        $data['eventInstances'] = TemplatedLink::factory([
            'rel' => 'eventInstances',
            'route' => 'e-camp-api.rest.doctrine.event-instance',
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
