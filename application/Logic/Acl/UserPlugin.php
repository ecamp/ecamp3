<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Logic\Acl;


class UserPlugin extends \Zend_Controller_Plugin_Abstract
{
	
	/** @var Zend_Acl */
	private $acl;
	
	
	/** @return Logic\Acl\CampPlugin */
	public function getAcl()
	{	return $this->acl;	}
	
	
	public function __construct()
	{
		$this->acl = new \Zend_Acl();
		
		$this->init();
	}
	
	
	private function init()
	{
		$this->acl->addRole(\Entity\User::ROLE_ANONYMOUS);
		$this->acl->addRole(\Entity\User::ROLE_USER);
		$this->acl->addRole(\Entity\User::ROLE_ADMIN);
		
		// @todo: Get List of Resources from config-file...
		// @todo: Get List of Resources from Reflection... 
		
		$this->acl->addResource("register");
		$this->acl->addResource("login");
		$this->acl->addResource("error");
		
		$this->acl->addResource("avatar");
		$this->acl->addResource("dashboard");
		$this->acl->addResource("user");
		$this->acl->addResource("group");
		$this->acl->addResource("camp");
		$this->acl->addResource("event");
		$this->acl->addResource("leader");
		
		
		
		$this->acl->allow(null, array("login", "register", "error"));
		$this->acl->allow(array(\Entity\User::ROLE_USER, \Entity\User::ROLE_ADMIN));
	}
	
	
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		$resourceName = $request->getControllerName();
		$privilegName = $request->getActionName();
		
		$user = \Logic\Auth\Plugin::getAuthenticatedUser();
		
		$role = \Entity\User::ROLE_ANONYMOUS;
		if(isset($user)){	$role = $user->getRole();	}

		if(! $this->acl->has($resourceName))
		{	$this->unknownResource();	}
		
		if(! $this->acl->hasRole($role))
		{	$this->unknownRole();	}
		
		
		if(! $this->acl->isAllowed($role, $resourceName, $privilegName))
		{	
			$redirect = "";
			
			if($request instanceof \Zend_Controller_Request_Http)
			{	$redirect = $this->getRequest()->getPathInfo();	}
			
			$request->setControllerName('login');
			$request->setActionName('index');
			$request->setParam('redirect', $redirect);
			
			$request->setDispatched(false);
		}
		
	}
	
	
	
	private function deny()
	{
		
		
	}
	
	
	private function unknownResource()
	{
		throw new \Exception("UserAcl - Unknown resource!");
	}
	
	
	private function unknownRole()
	{
		throw new \Exception("UserAcl - Unknown role!");
	}
	
}
