<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\User;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

abstract class AbstractService {
    private ServiceUtils $serviceUtils;
    private AuthenticationService $authenticationService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService
    ) {
        $this->serviceUtils = $serviceUtils;
        $this->authenticationService = $authenticationService;
    }

    protected function getServiceUtils(): ServiceUtils {
        return $this->serviceUtils;
    }

    protected function getOrigEntityData(BaseEntity $entity): array {
        return $this->serviceUtils->emGetOrigEntityData($entity);
    }

    protected function getAuthUser(): ?User {
        /** @var User $user */
        $user = null;

        if ($this->authenticationService->hasIdentity()) {
            $userRepository = $this->serviceUtils->emGetRepository(User::class);
            $userId = $this->authenticationService->getIdentity();
            $user = $userRepository->find($userId);
        }

        return $user;
    }

    /**
     * @param null $resource
     * @param null $privilege
     */
    protected function isAllowed($resource, $privilege = null): bool {
        $user = $this->getAuthUser();

        return $this->serviceUtils->aclIsAllowed($user, $resource, $privilege);
    }

    /**
     * @param null $resource
     * @param null $privilege
     *
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    protected function assertAllowed($resource, $privilege = null): void {
        $user = $this->getAuthUser();
        $this->serviceUtils->aclAssertAllowed($user, $resource, $privilege);
    }
}
