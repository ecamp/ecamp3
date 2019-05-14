<?php
namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class IndexController extends AbstractActionController
{
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


    public function indexAction()
    {
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
        } else {
            $data['user'] = 'guest';
        }

        $data['login'] = Link::factory([
            'rel' => 'login',
            'route' => 'e-camp-api.rpc.login'
        ]);


        $data['docu'] = Link::factory([
            'rel' => 'docu',
            'route' => 'zf-apigility/swagger'
        ]);

        $data['admin'] = Link::factory([
            'rel' => 'admin',
            'route' => 'zf-apigility/ui'
        ]);

//        $camps = new Link('camps');
//        $camps->setRoute('ecamp.api.camp');
//        $data['camps'] = $camps;

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
