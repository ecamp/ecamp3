<?php
namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hal\Link;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
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
            'route' => 'e-camp-api.rpc.index'
        ]);

        $data['api'] = Link::factory([
            'rel' => 'api',
            'route' => 'e-camp-api.rpc.api'
        ]);

        $data['setup'] = Link::factory([
            'rel' => 'setup',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'setup.php'
                ]
            ]
        ]);

        $data['php-info'] = Link::factory([
            'rel' => 'php-info',
            'route' => [
                'name' => 'e-camp-api.rpc.index',
                'params' => [
                    'action' => 'info.php'
                ]
            ]
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
        if ($userId != null) {
            $user = $this->userService->fetch($userId);
        }
        if ($user != null) {
            $data['user'] = $user->getDisplayName();

            $data['profile'] = Link::factory([
                'rel' => 'profile',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.user',
                    'params' => [ 'user_id' => $userId ]
                ]
            ]);
        } else {
            $data['user'] = 'guest';
        }

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.api'
        ]);

        $data['auth'] = Link::factory([
            'rel' => 'auth',
            'route' => 'e-camp-api.rpc.auth'
        ]);

        $data['docu'] = Link::factory([
            'rel' => 'docu',
            'route' => 'zf-apigility/swagger'
        ]);

        $data['admin'] = Link::factory([
            'rel' => 'admin',
            'route' => 'zf-apigility/ui'
        ]);

        $data['camp_list'] = Link::factory([
            'rel' => 'camp_list',
            'route' =>  'e-camp-api.rest.doctrine.camp',
        ]);

        $data['camps'] = Link::factory([
            'rel' => 'camps',
            'route' => [
                'name' => 'e-camp-api.rest.doctrine.camp',
                'params' => [ 'camp_id' => Link::tplParam('{id}') ],
            ]
        ]);

        $data['camps_query'] = Link::factory([
            'rel' => 'camps_query_not_working',
            'route' => [
                'name' => 'e-camp-api.rest.doctrine.camp',
                'params' => [ 'camp_id' => Link::tplParam('{id}') ],
                'options' => [
                    'query' => [
                        'a' => 'b'
                    ]
                ]
            ]
        ]);



        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
