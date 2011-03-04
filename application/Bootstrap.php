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

        $bisnaAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($bisnaAutoloader, 'loadClass'), 'Bisna');

        $appAutoloader = new \Doctrine\Common\ClassLoader('Inject');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Inject');


		$entityAutoloader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($entityAutoloader, 'loadClass'), 'Entity');

		$providerAutoloader = new \Doctrine\Common\ClassLoader('Logic', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($providerAutoloader, 'loadClass'), 'Logic');

		$pModAutoloader = new \Doctrine\Common\ClassLoader('PMod', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($pModAutoloader, 'loadClass'), 'PMod');
    }

	public function _initInjectionKernel()
	{
		$kernel = new \Inject\Kernel();

		$kernel
			->Bind("EntityManager")
			->ToProvider(new Logic\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new Logic\Provider\Repository("eCamp\Entity\Camp"));

		$kernel->Bind("SomeService")->ToSelf()->AsSingleton();

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


	protected function _initRoutes()
	{


		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'ControllerAction', new Zend_Controller_Router_Route(':controller/:action/*',
			array('controller' => 'index', 'action' => 'index')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'EntityId', new Zend_Controller_Router_Route(':controller/:action/:EntityId/*',
			array('controller' => 'index', 'action' => 'index'),
			array('EntityId' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CampId', new Zend_Controller_Router_Route(':CampId/:controller/:action/*',
			array('controller' => 'index', 'action' => 'index'),
			array('CampId' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CampEntityId', new Zend_Controller_Router_Route(':CampId/:controller/:action/:EntityId/*',
			array('controller' => 'index', 'action' => 'index'),
			array('CampId' => '\d+', 'EntityId' => '\d+')));


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

		// Register the translator for system-wide use based on browser settings
		// TODO: change to user settings later
		$locale = new Zend_Locale();
		switch($locale->getLanguage())
		{
			case 'de':
				$locale = 'de';
				break;
			default:
				$locale = 'en';
				break;
		}

		// Create the translators
		$translator = new Zend_Translate(array(
			'adapter' => 'gettext',
			'content' => $languagesPath . '/'.$locale.'/default.mo',
			'locale'  => $locale));
		Zend_Registry::set('Zend_Translate', $translator);

		// Register the locale for system-wide use
		Zend_Registry::set('Zend_Locale', new Zend_Locale($locale));

		// translate validation messages
		$validation_translator = new Zend_Translate(array(
			'adapter' => 'array',
			'content' =>  APPLICATION_PATH . '/../resources/languages/'.$locale.'/Zend_Validate.php',
			'locale'  => 'de'));
		Zend_Validate_Abstract::setDefaultTranslator($validation_translator);
	}

	protected function _initView()
	{
		$view = new Zend_View();
		
		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		return $view;
	}
}

