<?php

namespace Core\Acl;


use Core\Service\ServiceWrapper;
use CoreApi\Entity\User;

class DefaultAcl extends \Zend_Acl
{
	const GUEST 				= 'guest';
	const MEMBER				= 'member';
	const ADMIN 				= 'admin';
	const ADMIN_IN_USER_VIEW 	= 'admin_in_user_view';
	
	const CAMP_GUEST			= 'camp_guest';
	const CAMP_MEMBER			= 'camp_member';
	const CAMP_MANAGER			= 'camp_manager';
	const CAMP_OWNER			= 'camp_owner';
	const CAMP_CREATOR			= 'camp_creator';
	
	const GROUP_MEMBER			= 'group_member';
	const GROUP_MANAGER			= 'group_manager';
	
	const USER_FRIEND			= 'user_friend';
	const USER_ME				= 'user_me';
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	
	/**
	 * @var Core\Acl\ContextStorage
	 * @Inject Core\Acl\ContextStorage
	 */
	protected $contextStorage;
	
	/**
	 * @var array
	 */
	private $rolesCache = array();
	
	
	public function getRolesInContext()
	{
		$context = $this->contextStorage->getContext();
		$contextKey = (string) $context;
		
		$roles = array_key_exists($contextKey, $this->rolesCache) ?
			$roles = $this->rolesCache[$contextKey] : 
			$this->calculateRolesFromContext($context);
		
		return $roles;
	}
	
	
	private function calculateRolesFromContext($context)
	{		
		if(is_null($context->getMe()))
		{	return array(self::GUEST);	}

		$roles = array();
		
		/* roles without context */
		$me = $context->getMe();
		if($me)
		{
			if($me->getRole() == User::ROLE_ADMIN)
			{	$roles[] = self::ADMIN;		}
			else
			{	$roles[] = self::MEMBER;	}
			
			if(	$me != $this->contextStorage->getAuthUser() && 
				$this->contextStorage->getAuthUser()->getRole() == User::ROLE_ADMIN)
			{	$roles[] = self::ADMIN_IN_USER_VIEW;	}
		}
		else
		{	$roles[] = self::GUEST;		}
		
		
		/* roles in group context */
		$group = $context->getGroup();
		if($group)
		{
			if( $group->isManager($me) )
			{
				$roles[] = self::GROUP_MANAGER;
			}
			
			if( $group->isMember($me) )
			{
				$roles[] = self::GROUP_MEMBER;
			}
			
		}
		
		/* roles in camp context */
		$camp = $context->getCamp(); 
		if($camp)
		{
			if( $camp->getOwner() == $me )
			{
				$roles[] = self::CAMP_OWNER;
			}	
			
			if( $camp->getCreator() == $me)
			{
				$roles[] = self::CAMP_CREATOR;
			}
			
			if( $camp->isManager($me) )
			{
				$roles[] = self::CAMP_MANAGER;
			}
			
			if( $camp->isMember($me) )
			{
				$roles[] = self::CAMP_MEMBER;
			}
		}
		
		/* roles in user context */
		$user = $context->getUser();
		if($user)
		{
				if( $user == $me )
				{
					$roles[] = self::USER_ME;
				}
				else if( $user->isFriendOf($me) )
				{
					$roles[] = self::USER_FRIEND;
				}
		}
		
		$this->rolesCache[(string)$context] = $roles;
		
		return $roles;
	}
	
    /**
     * Setup roles
     */
    public function __construct()
    {
    	/* support roles */
    	$this->addRole(self::ADMIN_IN_USER_VIEW);
    	
    	/* general roles */
        $this->addRole(self::GUEST)
             ->addRole(self::MEMBER, 		self::GUEST)
             ->addRole(self::ADMIN,  		self::MEMBER);
             
        /* roles in context to a camp */
        $this->addRole(self::CAMP_GUEST)
             ->addRole(self::CAMP_MEMBER, 	self::CAMP_GUEST)
             ->addRole(self::CAMP_MANAGER, 	self::CAMP_MEMBER)
             ->addRole(self::CAMP_OWNER, 	self::CAMP_MANAGER)
        	 ->addRole(self::CAMP_CREATOR, 	self::CAMP_MANAGER);
             
        /* roles in context to a group */
        $this->addRole(self::GROUP_MEMBER)
             ->addRole(self::GROUP_MANAGER, self::GROUP_MEMBER);
        
        /* roles in context to another user */
        $this->addRole(self::USER_FRIEND)
			 ->addRole(self::USER_ME, 		self::USER_FRIEND);
    }
}