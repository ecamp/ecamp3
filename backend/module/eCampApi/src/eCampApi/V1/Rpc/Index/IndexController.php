<?php

namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hal\TemplatedLink;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;

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
            // BUG: throws error is $userId is not found
            $user = $this->userService->fetch($userId);
        }
        if (null != $user) {
            $data['user'] = $user->getDisplayName();

            $data['profile'] = Link::factory([
                'rel' => 'profile',
                'route' => 'e-camp-api.rpc.profile',
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
            'route' => 'api-tools/swagger',
        ]);

        $data['admin'] = Link::factory([
            'rel' => 'admin',
            'route' => 'api-tools/ui',
        ]);

        $data['users'] = TemplatedLink::factory([
            'rel' => 'users',
            'route' => 'e-camp-api.rest.doctrine.user',
        ]);

        $data['camps'] = TemplatedLink::factory([
            'rel' => 'camps',
            'route' => 'e-camp-api.rest.doctrine.camp',
        ]);

        $data['scheduleEntries'] = TemplatedLink::factory([
            'rel' => 'scheduleEntries',
            'route' => 'e-camp-api.rest.doctrine.schedule-entry',
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
