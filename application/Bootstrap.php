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
	 * Tells ZendFramework, where to find the Modules;
	 * the ModuleDirectory APPLICATION_PATH/Module/ is added to the ModuleDirectories.
	 */
	public function _initModuleDirectory()
	{
		$front = \Zend_Controller_Front::getInstance();
		$front->addModuleDirectory(APPLICATION_PATH . "/Module/");
	}
	
	
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
			->Bind("CoreApi\Service\Login\CampService")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Login\CampService"))
			->AsSingleton();
		
		$kernel
			->Bind("CoreApi\Service\Login\CampServiceValidator")
			->ToFactory(new \Core\Acl\ACWrapperFactory("CoreApi\Service\Login\CampServiceValidator"))
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

