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

use Core\Acl\ContextFactory;
use Core\Provider\EntityManager;
use Core\Provider\Repository;
use Core\Service\ServiceFactory;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	
	/**
	 * Loads the CoreNamespace to the Autoloader
	 */
	public function _initAutoloader()
	{
		require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';
		require_once APPLICATION_PATH . '/CoreApi/EntityClassLoader.php';
				
		$autoloader = \Zend_Loader_Autoloader::getInstance();
		
		$coreApiEntityAutoloader = new \CoreApi\EntityClassLoader();
		$autoloader->pushAutoloader($coreApiEntityAutoloader, 'CoreApi\Entity');
		
		$coreApiAutoloader = new \Doctrine\Common\ClassLoader('CoreApi', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($coreApiAutoloader, 'loadClass'), 'CoreApi');
		
		$coreAutoloader = new \Doctrine\Common\ClassLoader('Core', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($coreAutoloader, 'loadClass'), 'Core');
	}
	
	
	protected function _initRoutes()
	{
		$urlParts = explode('.', $_SERVER['HTTP_HOST']);
		
		$hostname = array_pop($urlParts);
		$hostname = array_pop($urlParts) . "." . $hostname;
		
		Zend_Registry::set('hostname', $hostname);
		
		
		// TODO: Try to remove this lines:
		// This adds the www - subdomain as default, if there is no subdomain
		if($_SERVER['HTTP_HOST'] == "www." . $hostname)
		{	$_SERVER['HTTP_HOST'] = $hostname;	}
		
	}
	
	
	protected function _initBasicErrorHandler()
	{
		$this->bootstrap('frontcontroller');
		$front = $this->getResource('frontcontroller');
		
		$plugin = new Zend_Controller_Plugin_ErrorHandler();
		$plugin->setErrorHandlerModule('WebApp');
		$plugin->setErrorHandlerController('Error');
		$plugin->setErrorHandlerAction('error');
		
		$front->registerPlugin($plugin);
	}
	
	
	/**
	 * Tells ZendFramework, where to find the Modules;
	 * the ModuleDirectory APPLICATION_PATH/Module/ is added to the ModuleDirectories.
	 */
	public function _initLoadRequiredModule()
	{
		$front = \Zend_Controller_Front::getInstance();
		$front->setModuleControllerDirectoryName("Controller");
		$front->addModuleDirectory(APPLICATION_PATH . "/Module/");
		
		$front->setParam('useDefaultControllerAlways', false);
	}
	
    
	public function _initInjectionKernel()
	{
		$kernel = new \PhpDI\Kernel();
		
		$kernel->Bind("PhpDI\IKernel")->ToConstant($kernel);
		
		$kernel->Bind("Core\Acl\DefaultAcl")->ToSelf()->AsSingleton();
		$kernel->Bind("Core\Acl\ContextStorage")->ToSelf()->AsSingleton();
		$kernel->Bind("CoreApi\Acl\ContextManager")->ToSelf()->AsSingleton();
		
		$kernel->Bind("Core\Acl\Context")->ToFactory(new ContextFactory(false));
		$kernel->Bind("CoreApi\Acl\Context")->ToFactory(new ContextFactory(true));
		
		
		$kernel->Bind("EntityManager")->ToProvider(new EntityManager());
		$kernel->Bind("Doctrine\ORM\EntityManager")->ToProvider(new EntityManager());

		
		$kernel->Bind("CampRepository")->ToProvider(new Repository("Core\Entity\Camp"));
		$kernel->Bind("GroupRepository")->ToProvider(new Repository("Core\Entity\Group"));
		$kernel->Bind("LoginRepository")->ToProvider(new Repository("Core\Entity\Login"));
		$kernel->Bind("UserRepository")->ToProvider(new Repository("Core\Entity\User"));
		$kernel->Bind("UserCampRepository")->ToProvider(new Repository("Core\Entity\UserCamp"));
		
		$kernel->Bind("Core\Repository\LoginRepository")->ToProvider(new Repository("Core\Entity\Login"));
		$kernel->Bind("Core\Repository\UserRepository")->ToProvider(new Repository("Core\Entity\User"));
		$kernel->Bind("Core\Repository\GroupRepository")->ToProvider(new Repository("Core\Entity\Group"));
		$kernel->Bind("Core\Repository\CampRepository")->ToProvider(new Repository("Core\Entity\Camp"));
		
		
		$kernel	->Bind("CoreApi\Service\UserService")
				->ToFactory(new ServiceFactory("CoreApi\Service\UserService"))
				->AsSingleton();
		
		$kernel	->Bind("CoreApi\Service\LoginService")
				->ToFactory(new ServiceFactory("CoreApi\Service\LoginService"))
				->AsSingleton();
		
		$kernel	->Bind("CoreApi\Service\CampService")
				->ToFactory(new ServiceFactory("CoreApi\Service\CampService"))
				->AsSingleton();
		
		
		Zend_Registry::set("kernel", $kernel);
	}


	/**
	 * Basic setup of module support and layout support.
	 *
	 * @return void
	 */
	protected function _initBasicConfig()
	{
		// Set the timezone default
		date_default_timezone_set('Europe/Zurich');
	}
}

