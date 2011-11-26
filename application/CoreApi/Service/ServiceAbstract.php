<?php

namespace CoreApi\Service;

/**
 * Abstract serivce
 */
abstract class ServiceAbstract
    implements \Zend_Acl_Resource_Interface
{
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;
	
	
    /**
     * ACL object
     *
     * @var App_Model_Acl
     */
    protected $_acl;

    /**
     * Resource ID of the service
     *
     * @var string
     */
    protected $_resource;

    /**
     * Get ACL
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {	
        if (null === $this->_acl) {
            $this->_acl = new \Core\DefaultAcl();
            $this->_acl->add($this);

            $this->_setupAcl();
        }

        return $this->_acl;
    }

    /**
     * Setup ACL
     *
     * @return void
     */
    protected function _setupAcl()
    {
    }

    /**
     * Check if a specific user has access to the given resource
     *
     * @param  string                  $permissions
     * @param  Zend_Acl_Role_Interface $user
     * @return boolean
     */
    public function checkAcl($permissions, Zend_Acl_Role_Interface $user = null)
    {
        if (null === $user) {
        	$userService = new User();
            $userRoles = $userService->getCurrentUserRole();
        }
        
        if( is_array($userRoles) ){
        	foreach( $userRoles as $role) {
        		if( $this->getAcl()->isAllowed($role, $this, $permissions) )
        			return true;
        	}
        	
        	return false;
        }
        else{
        	return $this->getAcl()->isAllowed($userRoles, $this, $permissions);
        }
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
        if( !method_exists($this, $method) ) {
            throw new \Exception("unknown method [$method]");
        }
        
        if( !$this->checkAcl($method) )
        	throw new \Ecamp\PermissionException("");
        
        return call_user_func_array(
            array($this, $method),
            $args
        );
    }

    /**
     * @see    Zend_Acl_Resource_Interface::getResourceId()
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resource;
    }
}