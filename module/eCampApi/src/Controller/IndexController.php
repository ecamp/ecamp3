<?php

namespace eCamp\Api\Controller;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Service\UserService;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class IndexController extends AbstractActionController {
    /** @var AuthService */
    private $authService;

    /** @var UserService */
    private $userService;

    public function __construct(
        AuthService $authService,
        UserService $userService
    ) {
        $this->authService = $authService;
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

        $userId = $this->authService->getAuthUserId();
        if ($userId != null) {
            $user = $this->userService->fetch($userId);
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
