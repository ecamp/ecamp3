<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Acl\NotAuthenticatedException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class AclService {
    private ServiceUtils $serviceUtils;
    private AuthenticationService $authenticationService;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        $this->serviceUtils = $serviceUtils;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param null|mixed $privilege
     *
     * @throws NoAccessException
     * @throws NotAuthenticatedException
     */
    public function assertAllowed($resource, $privilege = null): void {
        $user = $this->getAuthUser();
        if (null == $user) {
            throw new NotAuthenticatedException();
        }
        $this->serviceUtils->aclAssertAllowed($user, $resource, $privilege);
    }

    private function getAuthUser(): ?User {
        /** @var User $user */
        $user = null;

        if ($this->authenticationService->hasIdentity()) {
            $userRepository = $this->serviceUtils->emGetRepository(User::class);
            $userId = $this->authenticationService->getIdentity();
            $user = $userRepository->find($userId);
        }

        return $user;
    }
}
