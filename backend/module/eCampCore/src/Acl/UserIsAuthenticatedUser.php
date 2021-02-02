<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\User;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class UserIsAuthenticatedUser implements AssertionInterface {
    private AuthenticationServiceInterface $authenticationService;

    public function __construct(AuthenticationServiceInterface $authenticationService) {
        $this->authenticationService = $authenticationService;
    }

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ): bool {
        if ($resource instanceof User) {
            $authUserId = $this->authenticationService->getIdentity();

            return $resource->getId() == $authUserId;
        }

        return false;
    }
}
