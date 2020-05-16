<?php

namespace eCampApi\V1\Rpc\Profile;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;

class ProfileController extends AbstractActionController {
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

        if ($this->authenticationService->hasIdentity()) {
            /** @var Request $request */
            $request = $this->getRequest();
            $method = $request->getMethod().'Action';

            $userId = $this->authenticationService->getIdentity();
            /** @var User $user */
            $user = $this->userService->fetch($userId);

            $data = call_user_func([$this, $method], $user);
        }

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }

    public function getAction(User $user) {
        return [
            'self' => Link::factory([
                'rel' => 'self',
                'route' => 'e-camp-api.rpc.profile',
            ]),
            'username' => $user->getUsername(),
            'displayName' => $user->getDisplayName(),
            'mail' => $user->getTrustedMailAddress(),
            'role' => $user->getRole(),
            'language' => $user->getLanguage(),
        ];
    }

    public function patchAction(User $user) {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];

        if (isset($data->language)) {
            $user->setLanguage($data->language);
        }

        return $this->getAction($user);
    }
}
