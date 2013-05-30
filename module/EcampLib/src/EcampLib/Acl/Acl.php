<?php

namespace EcampLib\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;

use EcampLib\Acl\Exception\NoAccessException;
use EcampLib\Acl\Exception\AuthenticationRequiredException;

class Acl 
	extends ZendAcl
{
	
	/**
	 * @var RoleFactoryInterface
	 */
	private $roleFactory = null;
	
	/**
	 * @return RoleFactoryInterface
	 */
	public function getRoleFactory(){
		if($this->roleFactory == null){
			$this->roleFactory = new RoleFactory();
		}
		return $this->roleFactory;
	}
	
	public function setRoleFactory(RoleFactoryInterface $roleFactory){
		$this->roleFactory = $roleFactory;
	}
	
	
	/**
	 * @var ResourceFactoryInterface
	 */
	private $resourceFactory = null;
	
	/**
	 * @return ResourceFactoryInterface
	 */
	public function getResourceFactory(){
		if($this->resourceFactory == null){
			$this->resourceFactory = new ResourceFactory();
		}
		return $this->resourceFactory;
	}
	
	public function setResourceFactory(ResourceFactoryInterface $resourceFactory){
		$this->resourceFactory = $resourceFactory;
	}
	
	
	
	
    public function isAllowed($role = null, $resource = null, $privilege = null){
		
    	$roleFactory = $this->getRoleFactory();
    	$_role = $roleFactory->createRole($role);
    	
    	$resourceFactory = $this->getResourceFactory();
    	$_resource = $resourceFactory->createResource($resource);
		
		return parent::isAllowed($_role, $_resource, $privilege);
	}
	
	public function isAllowedException($role = null, $resource = null, $privilege = null){
		
		if($this->isAllowed($role, $resource, $privilege)){
			return true;
		} else {
			if($role == null){
				throw new AuthenticationRequiredException();
			} else {
				throw new NoAccessException();
			}
		}
	}
}