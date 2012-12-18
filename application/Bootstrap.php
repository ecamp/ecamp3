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
use Core\Service\ServiceFactory;

use Bisna\Doctrine\Container as DoctrineContainer;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	
	/**
	 * Loads the CoreNamespace to the Autoloader
	 */
	public function _initAutoloader()
	{
		require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';
				
		$autoloader = \Zend_Loader_Autoloader::getInstance();
		
		$coreApiAutoloader = new \Doctrine\Common\ClassLoader('CoreApi', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($coreApiAutoloader, 'loadClass'), 'CoreApi');
		
		$coreAutoloader = new \Doctrine\Common\ClassLoader('Core', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($coreAutoloader, 'loadClass'), 'Core');
		
		$pluginAutoloader = new \Doctrine\Common\ClassLoader('Plugin', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($pluginAutoloader, 'loadClass'), 'Plugin');
	}
	
	
	protected function _initRoutes()
	{
		if(in_array('HTTP_HOST', $_SERVER))
		{
			$urlParts = explode('.', $_SERVER['HTTP_HOST']);
			
			$hostname = array_pop($urlParts);
			$hostname = array_pop($urlParts) . "." . $hostname;
			
			Zend_Registry::set('hostname', $hostname);
		}
		else
		{
			Zend_Registry::set('hostname', 'ecamp3.dev');
		}
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
		$kernel->Bind("CoreApi\Acl\ContextProvider")->ToSelf()->AsSingleton();
		
		$kernel->Bind("Doctrine\ORM\EntityManager")->ToProvider(new \Core\Provider\EntityManager());

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
	
	
	protected function _initSetupDoctrine()
	{
		$opt = $this->getOption('doctrine');
		
		
		$kernel = \Zend_Registry::get('kernel');
		
		$container = new DoctrineContainer($opt);
		
		\Zend_Registry::set('doctrine', $container);
		
		$em = $kernel->Get('Doctrine\ORM\EntityManager');
		$IdGenerator = $kernel->Get('Core\Entity\IdGenerator');
		$strategyListener = new \Core\Plugin\StrategyEventListener($em);
		$em->getEventManager()->addEventListener(array('prePersist', 'preRemove'), $IdGenerator);
		$em->getEventManager()->addEventSubscriber($strategyListener);
		
		return $container;
		
	}
	
	
	protected function _initRepositories()
	{
		$kernel = \Zend_Registry::get('kernel');
		
		$em = $kernel->Get('Doctrine\ORM\EntityManager');
		$metadatas = $em->getMetadataFactory()->getAllMetadata();
		
		foreach($metadatas as $md){
			if(! $md->getReflectionClass()->isAbstract())
			{
			
				$entityName = $md->getName();
				$repoName = str_replace("CoreApi", "Core", $entityName);
				$repoName = str_replace("Entity", "Repository", $repoName);
				$repoName .= "Repository";
				
				$kernel->Bind($repoName)->ToProvider(new \Core\Provider\Repository($entityName));
			}
		}
		
		/*
		$kernel->Bind("CampRepository")->ToProvider(new Repository("CoreApi\Entity\Camp"));
		$kernel->Bind("GroupRepository")->ToProvider(new Repository("CoreApi\Entity\Group"));
		$kernel->Bind("LoginRepository")->ToProvider(new Repository("CoreApi\Entity\Login"));
		$kernel->Bind("UserRepository")->ToProvider(new Repository("CoreApi\Entity\User"));
		$kernel->Bind("UserCampRepository")->ToProvider(new Repository("CoreApi\Entity\UserCamp"));
		
		$kernel->Bind("Core\Repository\LoginRepository")->ToProvider(new Repository("CoreApi\Entity\Login"));
		$kernel->Bind("Core\Repository\UserRepository")->ToProvider(new Repository("CoreApi\Entity\User"));
		$kernel->Bind("Core\Repository\GroupRepository")->ToProvider(new Repository("CoreApi\Entity\Group"));
		$kernel->Bind("Core\Repository\CampRepository")->ToProvider(new Repository("CoreApi\Entity\Camp"));
		$kernel->Bind("Core\Repository\UserCampRepository")->ToProvider(new Repository("CoreApi\Entity\UserCamp"));
		$kernel->Bind("Core\Repository\UserGroupRepository")->ToProvider(new Repository("CoreApi\Entity\UserGroup"));
		$kernel->Bind("Core\Repository\EventInstanceRepository")->ToProvider(new Repository("CoreApi\Entity\EventInstance"));
		$kernel->Bind("Core\Repository\UserRelationshipRepository")->ToProvider(new Repository("CoreApi\Entity\UserRelationship"));
		*/
	}
	
	
	protected function _initServices()
	{
		$kernel = \Zend_Registry::get('kernel');
		
		
		$servicePath = APPLICATION_PATH . "/CoreApi/Service/";

		$fi = new DirectoryIterator($servicePath);
		
		while($fi->valid())
		{
			if( $fi->current()->isDir() )
			{
				$fi->next();
				continue;
			}
		
			$file = $fi->current()->getBasename();
				
			if(! strrpos($file, "."))
			{
				$fi->next();
				continue;
			}
		
			$filename = substr($file, 0, strrpos($file, "."));
			$publicClassname = "CoreApi\Service\\" . $filename;
			$privateClassname = "Core\Service\\" . $filename;
				
			$kernel->Bind($publicClassname)->ToFactory(new ServiceFactory($publicClassname))->AsSingleton();
			$kernel->Bind($privateClassname)->To($publicClassname)->AsSingleton();
				
			$fi->next();
		}
	}
}

