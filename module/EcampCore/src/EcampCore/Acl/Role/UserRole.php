<?php

namespace EcampCore\Acl\Role;

use Zend\Permissions\Acl\Role\RoleInterface;
use EcampCore\Entity\User;

class UserRole
    implements RoleInterface
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getRoleId()
    {
        return 'EcampCore\Entity\User::' . $this->user->getId();
    }

}
