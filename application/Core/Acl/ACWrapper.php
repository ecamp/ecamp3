<?php

namespace Core\Acl;

use Core\Service\ValidationWrapper;

class ACWrapper
{
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	private $kernel;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	private $acl;
	
	/**
	 * The protected Resource
	 * @var Zend_Acl_Resource_Interface
	 */
	private $resource;
	
	
	public function __construct(\Zend_Acl_Resource_Interface $resource)
	{
		$this->resource = $resource;
	}
	
	public function postInject()
	{
		$this->acl->addResource($this->resource);
		$this->kernel->Inject($this->resource);
		
		unset($this->kernel);
	}
	
	
	
	public function __call($method, $args)
	{
		if(ValidationWrapper::getServiceNestingLevel() > 0 || $this->isAllowed($method))
		{
			return call_user_func_array(array($this->resource, $method), $args);
		}
		
		throw new \Exception("No Access on " . $this->resource->getResourceId() . "::" . $method);
	}
	
	
	public function __get($property)
	{
		if(ValidationWrapper::getServiceNestingLevel() > 0 || $this->isAllowed($property))
		{
			return $this->resource->{$property};
		}
		
		throw new \Exception("No Access on " . $this->resource->getResourceId() . "::" . $method);
	}
	
	
	private function isAllowed($privilege = NULL)
	{
		// TODO: Load current roles from $acl or form some AUTH mechanism.
		$roles = $this->acl->getRolesInContext();
		
		
		// TODO: Remove default return value
		// FOR DEVELOPING:
			//return true;
		
		foreach ($roles as $role)
		{
			if($this->acl->isAllowed($role, $this->resource, $privilege))
			{	return true;	}
		}
		
		return false;
	}
	
	
}
