<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\BelongsToCampInterface;
use eCamp\Core\Entity\BelongsToContentNodeTreeInterface;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class UserIsCollaborator implements AssertionInterface {
    private array $collaborationRoles;

    public function __construct(array $collaborationRoles) {
        $this->collaborationRoles = $collaborationRoles;
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null): bool {
        /** @var User $user */
        $user = $role;

        if ($resource instanceof BelongsToContentNodeTreeInterface) {
            $resource = $resource->getContentNode();
        }

        if ($resource instanceof BelongsToCampInterface) {
            $resource = $resource->getCamp();
        }

        if ($resource instanceof Camp) {
            if ($resource->getCreator() === $user) {
                return true;
            }
            if ($resource->getOwner() === $user) {
                return true;
            }
            if ($resource->getCampCollaborations()->exists(function ($key, CampCollaboration $cc) use ($user) {
                return ($cc->getUser() === $user)
                    && ($cc->isEstablished())
                    && (in_array($cc->getRole(), $this->collaborationRoles));
            })) {
                return true;
            }
        }

        return false;
    }
}
