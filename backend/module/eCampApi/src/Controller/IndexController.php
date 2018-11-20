<?php

namespace eCamp\Api\Controller;

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


    /**
     * @return \Zend\View\Model\ViewModel|HalJsonModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {

        // Login-Info
        // Login-Endpoint

        // MyCamps
        // My....

        $data = [];
        $data['title'] = 'eCamp V3 - API';

        $user = null;
        $userId = $this->authenticationService->getIdentity();
        if ($userId != null) {
            $user = $this->userService->fetch($userId);
        }
        if ($user != null) {
            $data['user'] = $user->getDisplayName();
        }

        $data['login'] = Link::factory([
                'rel' => 'login',
                'route' => 'ecamp.api/login'
            ]);

        $camps = new Link('camps');
        $camps->setRoute('ecamp.api.camp');
        $data['camps'] = $camps;

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
