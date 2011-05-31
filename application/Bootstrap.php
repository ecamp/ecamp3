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
	 * @return void
	 */
	public function _initAutoloader()
    {
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();

		$entityAutoloader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($entityAutoloader, 'loadClass'), 'Entity');
		
		$pluginAutoloader = new \Doctrine\Common\ClassLoader('Plugin', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($pluginAutoloader, 'loadClass'), 'Plugin');

		$formAutoloader = new \Doctrine\Common\ClassLoader('Form', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($formAutoloader, 'loadClass'), 'Form');

	    $controllerAutoloader = new \Doctrine\Common\ClassLoader('Controller', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($controllerAutoloader, 'loadClass'), 'Controller');

	    $controllerAutoloader = new \Doctrine\Common\ClassLoader('Form', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($controllerAutoloader, 'loadClass'), 'Form');

		$providerAutoloader = new \Doctrine\Common\ClassLoader('Logic', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($providerAutoloader, 'loadClass'), 'Logic');

		$serviceAutoloader = new \Doctrine\Common\ClassLoader('Service', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($serviceAutoloader, 'loadClass'), 'Service');
    }

	public function _initInjectionKernel()
	{
		$kernel = new \Inject\Kernel();

		$kernel
			->Bind("EntityManager")
			->ToProvider(new Logic\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new Logic\Provider\Repository("Entity\Camp"));

		$kernel
			->Bind("LoginRepository")
			->ToProvider(new Logic\Provider\Repository("Entity\Login"));

		$kernel
			->Bind("UserRepository")
			->ToProvider(new Logic\Provider\Repository("Entity\User"));

		$kernel
			->Bind("UserCampRepository")
			->ToProvider(new Logic\Provider\Repository("Entity\UserCamp"));

		$kernel->Bind("Service\UserService")->ToSelf()->AsSingleton();

		Zend_Registry::set("kernel", $kernel);
	}

	/**
	 * Override the default Zend_View with Ztal support and configure defaults.
	 *
	 * @return void
	 */
	protected function _initZtal()
	{
		//configure an autoload prefix for Ztal
		Zend_Loader_Autoloader::getInstance()->registerNamespace('Ztal');
		
		//register the Ztal plugin
		$plugin = new Ztal_Controller_Plugin_Ztal($this->getOption('ztal'));
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
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

		// Configure the app namespace
		$this->setAppNamespace('Application');

		// create the app space autoloader
		new Zend_Application_Module_Autoloader(array(
			'basePath' => APPLICATION_PATH,
			'namespace' => 'Application',));


		//configure zend_layout
		Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . '/layouts/scripts'));
	}

	protected function _initRoutes()
	{
		
		/* general */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'general', new Zend_Controller_Router_Route(':controller/:action/*',
			array('controller' => 'dashboard', 'action' => 'index')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'general+id', new Zend_Controller_Router_Route(':controller/:action/:id/*',
			array('controller' => 'dashboard', 'action' => 'index'),
			array('id' => '\d+')));
				
		/* user */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'user', new Ecamp\Route\Vanity('user/:user/:action/*',
				array('controller' => 'user','action' => 'show')));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'user+id', new Ecamp\Route\Vanity('user/:user/:action/:id/*',
				array('controller' => 'user','action' => 'show'),
				array('id' => '\d+')));
		
		/* user camp */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'user+camp', new Ecamp\Route\Vanity('user/:user/:camp/:controller/:action/*',
				array('controller' => 'camps','action' => 'show')));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'user+camp+id', new Ecamp\Route\Vanity('user/:user/:camp/:controller/:action/:id/*',
				array('controller' => 'camps','action' => 'show'),
				array('id' => '\d+')));
				
		/* group */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'group', new Ecamp\Route\Vanity('group/:group/:action/*',
				array('controller' => 'group','action' => 'show')));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'group+id', new Ecamp\Route\Vanity('group/:group/:action/:id/*',
				array('controller' => 'group','action' => 'show'),
				array('id' => '\d+')));
				
		/* group camp */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'group+camp', new Ecamp\Route\Vanity('group/:group/:camp/:controller/:action/*',
				array('controller' => 'camps','action' => 'show')));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'group+camp+id', new Ecamp\Route\Vanity('group/:group/:camp/:controller/:action/:id/*',
				array('controller' => 'camps','action' => 'show'),
				array('id' => '\d+')));
				
		/* TODO: quick camp url */
	}

	/**
	 * Init translation services and locale.
	 *
	 * @return void
	 */
	protected function _initTranslationService()
	{
		// Build the path for the languages folder in the current module
		$languagesPath = APPLICATION_PATH . '/languages';

		// Setup a cache
		$frontendOptions = array();
		$backendOptions = array();

		$frontendOptions['automatic_serialization'] = true;
		$frontendOptions['lifetime'] = '604800';
		$frontendOptions['write_control'] = false;
		$frontendOptions['master_files'] = array($languagesPath . '/en/default.mo');

		$backendOptions['cache_dir'] = APPLICATION_PATH . '/../tmp/';
		$backendOptions['hashed_directory_level'] = 1;

		$cache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);

		Zend_Translate::setCache($cache);
		Zend_Locale::setCache($cache);

		// Create the translators
		$translator_en = new Zend_Translate(array(
			'adapter' => 'gettext',
			'content' => $languagesPath . '/en/default.mo',
			'locale'  => 'en'));

		$translator_de = new Zend_Translate(array(
			'adapter' => 'gettext',
			'content' => $languagesPath . '/de/default.mo',
			'locale'  => 'de'));

		// Register the translator for system-wide use based on browser settings
		// TODO: change to user settings later
		$locale = new Zend_Locale();
		switch($locale->getLanguage())
		{
			case 'de':
				Zend_Registry::set('Zend_Translate', $translator_de);
				break;
			default:
				Zend_Registry::set('Zend_Translate', $translator_en);
				break;
		}
		
		// Register the locale for system-wide use
		Zend_Registry::set('Zend_Locale', new Zend_Locale($locale->getRegion()));
	}

	protected function _initView()
	{
		$view = new Zend_View();

		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

		$view->headLink()->appendStylesheet('/css/blueprint/screen.css', 'screen, projection');
		$view->headLink()->appendStylesheet('/css/blueprint/ie.css', 'screen, projection', 'lt IE 8');
		$view->headLink()->appendStylesheet('/css/blueprint/print.css', 'print');

		$view->headLink()->appendStylesheet('/css/blueprint/plugins/fancy-type/screen.css', 'screen, projection');
		$view->headLink()->appendStylesheet('/css/blueprint/plugins/buttons/screen.css', 'screen, projection');
		
		$view->headLink()->appendStylesheet('/css/main.css');


		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		return $view;
	}
}

