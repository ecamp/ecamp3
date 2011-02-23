<?php
/**
 * PHPTal view subclass plugin to provide PHPTal support in Zend.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

require_once 'PHPTAL/PHPTAL.php';

/**
 * Overrides the default Zend View to provide Tal templating support through PHPTal
 *
 * Configurable options available through application.[ini|xml] on the bootstrap:
 * globalTemplatesDirectory[] - locations to look for additional templates
 *						(other than in application/layouts/scripts and [modules]/views/scripts)
 * customModifiersDirectory[] - directories to scan and load php files from in order to bring in custom
 *						modifiers and other code
 * encoding - the default encoding for template files (defaults to UTF-8)
 * cacheDirectory - the directory to use for caching compiled Tal templates
 *						(defaults to the systme tmp folder - usually /tmp/)
 * cachePurgeMode - sets whether to purge the cache after rendering (defaults to false)
 * highlightFailedTranslations - if a translator is installed, set whether failed transaction keys
 *						show up with a prepended '**'
 */

/**
 * PHPTal view subclass plugin to provide PHPTal support in Zend.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Controller_Plugin_Ztal extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * The config to use.
	 *
	 * @var array
	 */
	protected $_options;
	 
	 
	/**
	 * Constructor.
	 *
	 * @param array $options Configuration options.
	 */
	public function __construct($options)
	{
		$this->_options = $options;
	}
	
	/**
	 * Pre-dispatch hook to create and install a replacement View object.
	 *
	 * @param Zend_Controller_Request_Abstract $request The requst object.
	 *
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		// Unregister the built-in autoloader
		spl_autoload_unregister(array('PHPTAL','autoload'));
		
		// Register the autoloader through Zend
		Zend_Loader_Autoloader::getInstance()->pushAutoloader(
			array('PHPTAL','autoload'), 'PHPTAL');
		
		// We create an instance of our view wrapper and configure it
		// It extends Zend_View so we can configure it the same way
		$view = new Ztal_Tal_View($this->_options);
		
		if (Zend_Registry::isRegistered('Zend_Translate')) {
			//setup the translation facilities in PHPTal
			$translator = new Ztal_Tal_ZendTranslateTranslator($this->_options);
			$translator->useDomain($request->getControllerName());
			$view->getEngine()->setTranslator($translator);
		}
		
		// Call out to an overloadable method to pickup the paths for
		// templates for the current module
		foreach ($this->_currentModuleTemplatePaths($request) as $currentPath) {
			$view->addTemplateRepositoryPath($currentPath);
		}

		// We configure the view renderer in order to use our PHPTAL view
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setViewSuffix('phtml');
		$view->layout()->setViewSuffix('phtml');
		$viewRenderer->setView($view);
		Zend_Registry::set('Ztal_View', $view);
	}
	
	
	/**
	 * Easily overloadable method to supply the path to the module templates.
	 *
	 * @param Zend_Controller_Request_Abstract $request The request.
	 *
	 * @return array An array of paths to add to the template path set.
	 */
	protected function _currentModuleTemplatePaths(Zend_Controller_Request_Abstract $request)
	{
		$modulePath = Zend_Controller_Front::getInstance()->getModuleDirectory();
		return array(
			$modulePath . DIRECTORY_SEPARATOR . 'views',
			$modulePath . DIRECTORY_SEPARATOR . 'views'. DIRECTORY_SEPARATOR . 'scripts');
	}
	
}