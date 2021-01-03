<?php

namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hal\TemplatedLink;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController {
    /** @var UserService */
    private $userService;

    public function __construct(
        UserService $userService
    ) {
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
        $user = $this->userService->findAuthenticatedUser();

        if (null != $user) {
            $data['user'] = $user->getDisplayName();
            $data['authenticated'] = true;
        } else {
            $data['user'] = 'guest';
            $data['authenticated'] = false;
        }

        $data['profile'] = Link::factory([
            'rel' => 'profile',
            'route' => 'e-camp-api.rpc.profile',
        ]);

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

        $data['campTypes'] = TemplatedLink::factory([
            'rel' => 'campTypes',
            'route' => 'e-camp-api.rest.doctrine.camp-type',
        ]);

        $data['camps'] = TemplatedLink::factory([
            'rel' => 'camps',
            'route' => 'e-camp-api.rest.doctrine.camp',
        ]);

        $data['scheduleEntries'] = TemplatedLink::factory([
            'rel' => 'scheduleEntries',
            'route' => 'e-camp-api.rest.doctrine.schedule-entry',
        ]);

        $data['periods'] = TemplatedLink::factory([
            'rel' => 'periods',
            'route' => 'e-camp-api.rest.doctrine.period',
        ]);

        $data['activities'] = TemplatedLink::factory([
            'rel' => 'activities',
            'route' => 'e-camp-api.rest.doctrine.activity',
        ]);

        $data['materialItems'] = TemplatedLink::factory([
            'rel' => 'materialItems',
            'route' => 'e-camp-api.rest.doctrine.material-item',
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
