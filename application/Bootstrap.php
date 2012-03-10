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
		
		$kernel
			->Bind("EntityManager")
			->ToProvider(new Core\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\Camp"));

		$kernel
			->Bind("GroupRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\Group"));

		$kernel
			->Bind("LoginRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\Login"));

		$kernel
			->Bind("UserRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\User"));

		$kernel
			->Bind("UserCampRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\UserCamp"));
		
		$kernel
			->Bind("Core\Repository\LoginRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\Login"));
		
		$kernel
			->Bind("Core\Repository\UserRepository")
			->ToProvider(new Core\Provider\Repository("Core\Entity\User"));

		
		
		$kernel
			->Bind("CoreApi\Service\User\UserService")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\User\UserService"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\User\UserServiceValidator")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\User\UserServiceValidator"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\Login\LoginService")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Login\LoginService"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\Login\LoginServiceValidator")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Login\LoginServiceValidator"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\Camp\CampService")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Camp\CampService"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\Camp\CampServiceValidator")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Camp\CampServiceValidator"))
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

