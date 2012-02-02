<?php

namespace CoreApi\Service;

/**
 * Abstract serivce
 */
use Ecamp\Paginator\Doctrine;

abstract class ServiceBase
    implements \Zend_Acl_Resource_Interface
{
	
	/**
	 * EntityManager
	 * 
	 * @var Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;
	
	
    /**
     * ACL object
     *
     * @var Core\DefaultAcl
     * @Inject Core\DefaultAcl
     */
    protected $acl;


    /**
     * Calls the setupAcl Method to setup the AccessControlList
     */
    public function init()
    {
    	$this->getAcl()->addResource($this);
    	$this->setupAcl();
    }
    
    
    /**
     * Get ACL
     *
     * @return \Zend_Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Setup ACL
     *
     * @return void
     */
    protected function setupAcl()
    {}

    /**
     * Check if a specific user has access to the given resource
     *
     * @param  string                  $permissions
     * @param  Zend_Acl_Role_Interface $user
     * @return boolean
     */
    public function checkAcl($permissions, Zend_Acl_Role_Interface $user = null)
    {
        if (null === $user)
        {
            $userRoles = $this->getCurrentUserRole();
        }
        
        if( is_array($userRoles) )
        {
        	foreach( $userRoles as $role) 
        	{
        		if( $this->getAcl()->isAllowed($role, $this, $permissions) )
        		{
        			return true;
        		}
        	}
        	
        	return false;
        }
        else
        {
        	return $this->getAcl()->isAllowed($userRoles, $this, $permissions);
        }
    }
 
	
	
	/**
	 * 
	 * Return the set of roles for the current user based on the context (Group, Camp, User)
	 * @param unknown_type $group
	 * @param unknown_type $camp
	 */
	public function getCurrentUserRole($context = null)
	{
		/* this is only a dummy implemention which gives full access (top role for every context) */
		$roles = array();
		$roles[] = new \Zend_Acl_Role('member');
		$roles[] = new \Zend_Acl_Role('group_manager');
		$roles[] = new \Zend_Acl_Role('camp_owner');
		$roles[] = new \Zend_Acl_Role('user_me');
		
		return $roles;
	}
	
    
    /**
    * Magic method 
    * Makes protected/private methods available if allowed by ACL
    * @throws \Exception
    * @param  $function
    * @param  $args
    */
	public function __call($method, $args)
    {
        if( !method_exists($this, $method) )
        {
            throw new \Exception("unknown method [$method]");
        }
        
        if( !$this->checkAcl($method) )
        {
        	throw new \Ecamp\PermissionException("");
        }
        
        return call_user_func_array(array($this, $method), $args);
    }

    
    /**
     * @see    Zend_Acl_Resource_Interface::getResourceId()
     * @return string
     */
    public function getResourceId()
    {
        return get_class($this);
    }
}