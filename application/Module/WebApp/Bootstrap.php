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

class WebApp_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initBootstrapPlugin()
	{
		$this->getApplication()->bootstrap('frontcontroller');
		$front = $this->getApplication()->getResource('frontcontroller');

		$front->registerPlugin(new \Core\Module\BootstrapPlugin($this));
	}

	
	protected function _initAutoloader()
	{
		require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';
	
		$autoloader = \Zend_Loader_Autoloader::getInstance();
	
		$navigationAutoloader = new \Doctrine\Common\ClassLoader('WebApp', APPLICATION_PATH . '/Module/');
		$autoloader->pushAutoloader(array($navigationAutoloader, 'loadClass'), 'WebApp');
	}

	
	protected function _initRoutes()
	{
		$hostname = Zend_Registry::get('hostname');
		
		/* Subdomain Route */
		$webappSubdomain = new Zend_Controller_Router_Route_Hostname(
			$hostname, array('module' => 'WebApp'));
		
		
		
		/* default Moduel Router */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+general', $webappSubdomain->chain(
				new Zend_Controller_Router_Route('/:controller/:action/*',
				array(/*'module' => 'WebApp',*/ 'controller' => 'dashboard', 'action' => 'index'))));
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+general+id', $webappSubdomain->chain(
				new Zend_Controller_Router_Route('/:controller/:action/:id/*',
				array(/*'module' => 'WebApp',*/ 'controller' => 'dashboard', 'action' => 'index'),
				array('id' => '\d+'))));
		
		
		/* user */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+user', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('user/:user/:action/*',
				array('controller' => 'user','action' => 'show'))));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+user+id', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('user/:user/:action/:id/*',
				array('controller' => 'user','action' => 'show'),
				array('id' => '\d+'))));
		
		
		/* user camp */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+user+camp', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('user/:user/:camp/:controller/:action/*',
				array('controller' => 'camp','action' => 'show'))));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+user+camp+id', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('user/:user/:camp/:controller/:action/:id/*',
				array('controller' => 'camp','action' => 'show'),
				array('id' => '\d+'))));

		
		/* group */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+group', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('group/:group/:action/*',
				array('controller' => 'group','action' => 'show'))));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+group+id', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('group/:group/:action/:id/*',
				array('controller' => 'group','action' => 'show'),
				array('id' => '\d+'))));

		
		/* group camp */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+group+camp', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('group/:group/:camp/:controller/:action/*',
				array('controller' => 'camp','action' => 'show'))));
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'web+group+camp+id', $webappSubdomain->chain(
				new Ecamp\Route\Vanity('group/:group/:camp/:controller/:action/:id/*',
				array('controller' => 'camp','action' => 'show'),
				array('id' => '\d+'))));

		
		/* TODO: quick camp url */
	}
	
	
	public function _routeShutdown_RegisterContextPlugin()
	{
		$kernel = Zend_Registry::get("kernel");
		
		$contextPlugin = new WebApp\Acl\ContextPlugin();
		$kernel->Inject($contextPlugin);
		
		Zend_Controller_Front::getInstance()->registerPlugin($contextPlugin);
	}
	
	
	public function _routeShutdown_SetLayoutPath()
	{
		$layout = \Zend_Layout::startMvc();
		
		$layout->enableLayout();
		$layout->setLayoutPath(APPLICATION_PATH . "/Module/WebApp/layouts/scripts/");
	}
	
	
	/**
	 * Override the default Zend_View with Ztal support and configure defaults.
	 *
	 * @return void
	 */
	public function _routeShutdown_Ztal()
	{
		$this->getApplication()->bootstrap('frontcontroller');
		$front = $this->getApplication()->getResource('frontcontroller');
		
		//register the Ztal plugin
		$plugin = new Ztal_Controller_Plugin_Ztal($this->getOption('ztal'));
		$front->registerPlugin($plugin);
	}	
	
	
	/**
	 * Load and configure error handler
	 */
	public function _routeShutdown_ErrorHandler()
	{
		$plugin = new Zend_Controller_Plugin_ErrorHandler();
		$plugin->setErrorHandlerModule('WebApp');
		$plugin->setErrorHandlerController('Error');
		
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
	}
	
	
	/**
	 * Init translation services and locale.
	 *
	 * @return void
	 */
	public function _routeShutdown_TranslationService()
	{
		// Build the path for the languages folder in the current module
		$languagesPath = APPLICATION_PATH . '/Module/WebApp/languages';

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

	
	public function _routeShutdown_View()
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