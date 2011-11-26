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
    	/* general roles */
        $this->addRole('guest')
             ->addRole('member', 'guest')
             ->addRole('admin', 'member');
             
        /* roles in context to a camp */
        $this->addRole('camp_guest')
             ->addRole('camp_normal', 'camp_guest')
             ->addRole('camp_manager', 'camp_normal')
             ->addRole('camp_owner', 'camp_manager');
             
        /* roles in context to a group */
        $this->addRole('group_guest')
             ->addRole('group_member', 'group_guest')
             ->addRole('group_manager', 'group_member');
        
        /* roles in context to another user */
        $this->addRole('user_guest')
			->addRole('user_friend', 'user_guest')
			->addRole('user_me', 'user_friend');
    }
}