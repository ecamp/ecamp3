<?php

namespace eCampApi\V1\Rpc\Profile;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\View\ApiProblemModel;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * ProfileController
 * API Profile-Endpoint.
 * Detial-Information about authenticated User.
 */
class ProfileController extends AbstractActionController {
    private AuthenticationService $authenticationService;
    private UserService $userService;

    public function __construct(
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
    }

    public function indexAction(): ViewModel {
        if ($this->authenticationService->hasIdentity()) {
            /** @var Request $request */
            $request = $this->getRequest();
            $method = $request->getMethod().'Action';

            $userId = $this->authenticationService->getIdentity();
            /** @var User $user */
            $user = $this->userService->fetch($userId);

            return call_user_func([$this, $method], $user);
        }

        return new ApiProblemModel(new ApiProblem(401, null));
    }

    private function getAction(User $user): ViewModel {
        return new HalJsonModel(['payload' => new Entity([
            'self' => Link::factory([
                'rel' => 'self',
                'route' => 'e-camp-api.rpc.profile',
            ]),
            'user' => $user,
            'language' => $user->getLanguage(),
            'isAdmin' => (User::ROLE_ADMIN == $user->getRole()),
        ])]);
    }

    /**
     * @throws \Exception
     */
    private function patchAction(User $user): ViewModel {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];

        if (isset($data->language)) {
            $user->setLanguage($data->language);
        }

        // DEBUG-CODE
        // Used in ApiCheckbox on http://frontend/controls
        if (isset($data->isAdmin)) {
            $user->setRole($data->isAdmin ? User::ROLE_ADMIN : User::ROLE_USER);
        }

        return $this->getAction($user);
    }
}
