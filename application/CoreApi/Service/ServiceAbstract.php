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
    
	/* **************************************************** */
	
	protected $_forms = array();
	
	/**
	* Returns a form of the model
	* @param string $type
	* @return Zend_Form
	*/
	public function getForm($type = null)
	{
		$type = isset($type) ? $type : $this->defaultForm;
	
		$type  = ucfirst($type);
		if (!isset($this->_forms[$type])) {
			$class = '\Core\Form\\' . $this->get_class_name($this) . '\\' . $type;
			$this->_forms[$type] = new $class;
		}
		
		return $this->_forms[$type];
	}
	
	/**
	* Returns the name of a class using get_class with the namespaces stripped.
	* This will not work inside a class scope as get_class() a workaround for
	* that is using get_class_name(get_class());
	*
	* @param  object|string  $object  Object or Class Name to retrieve name
	
	* @return  string  Name of class with namespaces stripped
	*/
	protected function get_class_name($object = null)
	{
		if (!is_object($object) && !is_string($object)) {
			return false;
		}
	
		$class = explode('\\', (is_string($object) ? $object : get_class($object)));
		return $class[count($class) - 1];
	}
	
	/**
	 * Save data to the entity trough the validation/filter chain
	 * @param array $data
	 * @param null|string $form
	 * @param bool $partial
	 * @return bool
	 */
	public function save(array $data, $form = null, $partial = false)
	{
		$form = $this->getForm($form);
	
		/* save user entries in the form */
		$form->setAttribs($data);
		
		if( $partial )
		{
			return false;
			if (!$form->isValidPartial($data)) {
			}
		}
		else {
			if (!$form->isValid($data)) {
				return false;
			}
		}
	
		$this->updateAttributes($form->getValues());
	
		/* we could persist and flush ourselves here. needs to be discussed */
	
		return true;
	}
	
	/**
	 * Save data to the entity through the validation/filter chain (partialValidation)
	 */
	public function savePartial(array $data, $form = null)
	{
		return $this->save($data, $form, true);
	}
	
	/**
	 * update attributes of an entity by array
	 */
	protected function updateAttributes($data)
	{
		foreach( $data as $key=>$value )
		$this->{"set".ucfirst($key)}($value);
	}
	
}