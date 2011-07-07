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


class CampPlugin extends \Zend_Controller_Plugin_Abstract
{
	/** @var bool */
	private $disablePlugin = false;
	
	/** @var Zend_Config_Ini */
	private $config;
	
	/** @var Zend_Controller_Front */
	private $frontController;
	
	/** @var Zend_Acl */
	private $acl;
	
	
	/** @return Logic\Acl\CampPlugin */
	public function getAcl()
	{	return $this->acl;	}
	
	
	public function __construct()
	{
		$this->config = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/campAcl.ini', APPLICATION_ENV);
		
		$this->frontController = \Zend_Controller_Front::getInstance();
		
		$this->acl = new \Zend_Acl();
		
		$this->init();
	}
	
	
	private function init()
	{
		$this->acl->addRole('creator');
		
		if($this->config->get('Resource'))
		{
			foreach($this->config->Resource as $resource)
			{	$this->acl->addResource($resource->Name);	}
		}
		
		
		$this->acl->allow('creator');
	}
	
	
	public function dispatchLoopStartup(\Zend_Controller_Request_Abstract $request)
	{
		// Chech, if Plugin does have any relevance to requested Controller:
		$controllerName = $request->getControllerName();
		$controllerClass = $this->frontController->getDispatcher()->getControllerClass($request);
		
		if(is_null($this->config->get('Resource')->get($controllerClass)))
		{	$this->disablePlugin = true;	return;	}
		
		// Load Rules for definded camp; if no camp defined, disable plugin
		$campId = $request->getParam('camp');
		if(isset($campId))
		{	$this->loadRules($campId);	}
		else
		{	$this->disablePlugin = true;	return;	}
	}
	
	
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		if($this->disablePlugin){	return;	}
		
		
		$controllerName = $request->getControllerName();
		$actionName 	= $request->getActionName();

		$controllerClass = $this->frontController->getDispatcher()->getControllerClass($request);
		$controllerConfig = $this->config->get('Resource')->get($controllerClass);
		
		$resourceName = $controllerConfig->get('Name');
		$privilegeName = null;
		
		if(! is_null($controllerConfig->get('Privilege')))
		{
			$privilegeName = $controllerConfig->get('Privilege')->get($actionName);
			if(isset($privilegeName)){	$privilegeName = $privilegeName->get('Name'); }
		}
		
		$campId = $request->getParam('camp');
		$role = $this->getRole($campId);
				
		
		if(! $this->acl->has($resourceName))
		{	$this->unknownResource();	}
		
		if(! $this->acl->hasRole($role))
		{	$this->unknownRole();	}
		
		if(! $this->acl->isAllowed($role, $resourceName, $privilegeName))
		{	$this->deny();	}
	}
	
	
	
	private function loadRules($campId)
	{
		$camp = \Logic\Provider\Repository::Get("Entity\Camp")->find($campId);
		if(is_null($camp)){	return;	}
		
		foreach ($camp->getAclRoles() as $aclRole)
		{
			$this->acl->addRole($aclRole);
			
			foreach ($aclRole->getAclRules() as $aclRule)
			{
				$resource = $aclRule->getResource();
				$privileg = $aclRule->getPrivileg() ? $aclRule->getPrivileg() : null;
				
				if($aclRule->isAllowing())
				{	$this->acl->allow($aclRole, $resource, $privileg);	}
				
				elseif($aclRule->isDenying())
				{	$this->acl->deny($aclRole, $resource, $privileg);	}
			}
			
		}
	}
	
	
	/**
	 * @return \Entity\AclRole
	 */
	private function getRole($campId)
	{
		$user = \Logic\Auth\Plugin::getAuthenticatedUser();
		
		$userCamp = \Logic\Provider\Repository::Get("Entity\UserCamp")->findOneBy(
						array('user' => $user->getId(), 'camp' => $campId));
		
		if(is_null($userCamp))
		{
			$camp = \Logic\Provider\Repository::Get("Entity\Camp")->find($campId);
			
			if(is_null($camp) || $camp->getCreator() != $user)
			{	return null;	}
			else
			{	return "creator";	}
		}
		else
		{	return $userCamp->getAclRole();	}
	}
	
	
	private function deny()
	{
		throw new \Exception("CampAcl - Access denied!");
	}
	
	
	private function unknownResource()
	{
		throw new \Exception("CampAcl - Unknown resource!");
	}
	
	
	private function unknownRole()
	{
		throw new \Exception("CampAcl - Unknown role!");
	}
	
}
