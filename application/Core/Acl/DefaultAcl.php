<?php

namespace Core\Acl;


use Core\Entity\User;

class DefaultAcl extends \Zend_Acl
{
	const GUEST 		= 'guest';
	const MEMBER		= 'member';
	const ADMIN			= 'admin';
	
	
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	
	/**
	 * @var Core\Acl\Context
	 * @Inject Core\Acl\Context
	 */
	protected $context;
	
	
	
	
	
	public function getRolesInContext()
	{
		if(is_null($this->context->getMe()))
		{	return array(self::GUEST);	}
		
		$roles = array();
		
		
		if($this->context->getMe())
		{
			if($this->context->getMe()->getRole() == User::ROLE_ADMIN)
			{	$roles[] = self::ADMIN;		}
			else
			{	$roles[] = self::MEMBER;	}
		}
		else
		{	$roles[] = self::GUEST;		}
		
		
		
		
		if($this->context->getGroup())
		{
			
		}
		
		
		return $roles;
	}
	
    /**
     * Setup roles
     */
    public function __construct()
    {
    	/* general roles */
        $this->addRole(self::GUEST)
             ->addRole(self::MEMBER, self::GUEST)
             ->addRole(self::ADMIN,  self::MEMBER);
             
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