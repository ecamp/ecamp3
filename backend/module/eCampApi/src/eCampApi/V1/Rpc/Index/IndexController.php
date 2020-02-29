<?php
namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
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

        $data['auth'] = Link::factory([
            'rel' => 'auth',
            'route' => 'e-camp-api.rpc.auth'
        ]);

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.index'
        ]);


        $data['docu'] = Link::factory([
            'rel' => 'docu',
            'route' => 'zf-apigility/swagger'
        ]);

        $data['admin'] = Link::factory([
            'rel' => 'admin',
            'route' => 'zf-apigility/ui'
        ]);

        $data['camps'] = Link::factory([
            'rel' => 'camps',
            'route' => 'e-camp-api.rest.doctrine.camp'
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
