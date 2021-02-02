<?php

namespace eCamp\Lib\Acl;

use Laminas\Permissions\Acl\Acl as LaminasAcl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class Acl extends LaminasAcl {
    public const REST_PRIVILEGE_FETCH = 'fetch';
    public const REST_PRIVILEGE_FETCH_ALL = 'fetchAll';
    public const REST_PRIVILEGE_CREATE = 'create';
    public const REST_PRIVILEGE_PATCH = 'patch';
    public const REST_PRIVILEGE_UPDATE = 'update';
    public const REST_PRIVILEGE_DELETE = 'delete';

    /**
     * @param RoleInterface|string     $role
     * @param ResourceInterface|string $resource
     * @param string                   $privilege
     */
    public function isAllowed($role = null, $resource = null, $privilege = null): bool {
        if (null == $role) {
            $role = new Guest();
        }

        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * @param RoleInterface|string     $role
     * @param ResourceInterface|string $resource
     * @param string                   $privilege
     *
     * @throws NoAccessException
     */
    public function assertAllowed($role = null, $resource = null, $privilege = null) {
        if (!$this->isAllowed($role, $resource, $privilege)) {
            throw new NoAccessException();
        }
    }
}
