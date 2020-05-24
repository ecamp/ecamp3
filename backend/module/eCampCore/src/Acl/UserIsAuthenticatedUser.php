<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\User;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class UserIsAuthenticatedUser implements AssertionInterface {
    private $authUserId;

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if ($resource instanceof User) {
            if (null == $this->authUserId) {
                $authService = new AuthenticationService();
                $this->authUserId = $authService->getIdentity();
            }

            return $resource->getId() == $this->authUserId;
        }

        return false;
    }
}
