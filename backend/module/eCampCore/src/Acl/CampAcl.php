<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl as eCampAcl;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class CampAcl implements AssertionInterface {
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null) {
        /** @var User $user */
        $user = $role;
        /** @var Camp $camp */
        $camp = $resource;

        switch ($privilege) {
            case eCampAcl::REST_PRIVILEGE_FETCH_ALL:
            case eCampAcl::REST_PRIVILEGE_FETCH:
            case eCampAcl::REST_PRIVILEGE_CREATE:
            case eCampAcl::REST_PRIVILEGE_UPDATE:
            case eCampAcl::REST_PRIVILEGE_PATCH:
                return true;
                return false
                    || ($camp->getOwner() === $user)
                    || ($camp->getCreator() === $user)
                    // || ($camp->visibility)  // Camp Visibility PUBLIC
                ;

            case eCampAcl::REST_PRIVILEGE_DELETE:
                return false
                    || ($camp->getOwner() === $user)
                    || ($camp->getCreator() === $user)
                    // || ($camp->visibility)  // Camp Visibility PUBLIC
                    ;

            default:
                return false;
        }
    }
}
