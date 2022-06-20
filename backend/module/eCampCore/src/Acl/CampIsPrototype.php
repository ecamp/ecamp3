<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\BelongsToCampInterface;
use eCamp\Core\Entity\BelongsToContentNodeTreeInterface;
use eCamp\Core\Entity\Camp;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class CampIsPrototype implements AssertionInterface {
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null): bool {
        if ($resource instanceof BelongsToContentNodeTreeInterface) {
            $resource = $resource->getContentNode();
        }

        if ($resource instanceof BelongsToCampInterface) {
            $resource = $resource->getCamp();
        }

        if ($resource instanceof Camp) {
            return $resource->getIsPrototype();
        }

        return false;
    }
}
