<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\BelongsToCampInterface;
use eCamp\Core\Entity\BelongsToContentNodeInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class CampIsPrototype implements AssertionInterface {
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null): bool {
        if ($resource instanceof BelongsToContentNodeInterface) {
            $resource = $resource->getContentNode();
        }

        if ($resource instanceof BelongsToCampInterface) {
            $camp = $resource->getCamp();

            return $camp->getIsTemplate();
        }

        return false;
    }
}
