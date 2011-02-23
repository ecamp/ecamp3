<?php
/**
 * Zend Bootstrap.
 *
 * @category  Namesco
 * @package   Global
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Bootstrap.
 *
 * Performs sitewide Zend configuration for ControlPanel. Anything that needs
 * configuring but which should not be tweaked from the application.ini file
 * should be setup here, where possible.
 *
 * @category Namesco
 * @package  Global
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Basic setup of module support and layout support.
	 *
	 * @return void
	 */
	protected function _initBasicConfig()
	{
		// Set the timezone default
		date_default_timezone_set('Europe/London');


		//configure the front controller to use modules and throw exceptions
		Zend_Controller_Front::getInstance()
			->addModuleDirectory(APPLICATION_PATH . '/modules')
			->throwExceptions(true);

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
		$frontendOptions['master_files'] = array($languagesPath . '/en/messages.mo');
		
		$backendOptions['cache_dir'] = APPLICATION_PATH . '/../zendCache';
		$backendOptions['hashed_directory_level'] = 1;
		
		$cache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
		
		Zend_Translate::setCache($cache);
		Zend_Locale::setCache($cache);

		// Register the locale for system-wide use
		Zend_Registry::set('Zend_Locale', new Zend_Locale('en'));
		
		// Create a translator
		$translator = new Zend_Translate(array(
			'adapter' => 'gettext',
			'content' => $languagesPath . '/en/messages.mo',
			'locale'  => 'en'));

		// Register the translator for system-wide use
		Zend_Registry::set('Zend_Translate', $translator);
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
}
