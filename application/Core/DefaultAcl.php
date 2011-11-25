<?php

namespace Core;

/**
 * ACL model
 */
class DefaultAcl extends \Zend_Acl
{
    /**
     * Setup roles
     */
    public function __construct()
    {
        $this->addRole('guest')
             ->addRole('member', 'guest')
             ->addRole('admin', 'member');
             
        $this->addRole('camp_guest')
             ->addRole('camp_normal', 'camp_guest')
             ->addRole('camp_manager', 'camp_normal')
             ->addRole('camp_owner', 'camp_manager');
             
        $this->addRole('group_guest')
             ->addRole('group_member', 'group_guest')
             ->addRole('group_manager', 'group_member');
    }
}