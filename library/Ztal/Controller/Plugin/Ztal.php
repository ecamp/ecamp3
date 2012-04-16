<?php
/**
 * PHPTal view subclass plugin to provide PHPTal support in Zend.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

require_once 'PHPTAL/PHPTAL.php';

/**
 * Overrides the default Zend View to provide Tal templating through PHPTal.
 *
 * Configurable options available through application.[ini|xml] on the bootstrap:
 * globalTemplatesDirectory[] - locations to look for additional templates
 * customModifiersDirectory[] - directories to scan and load php files from to
 *                               bring in custom modifiers and other code
 * encoding - the default encoding for template files (defaults to UTF-8)
 * cacheDirectory - the directory to use for caching compiled Tal templates
 *						(defaults to the systme tmp folder - usually /tmp/)
 * cachePurgeMode - whether to purge the cache after rendering (default: false)
 * highlightFailedTranslations - if a translator is installed, set whether
 *                        failed transaction keys show up with a prepended '**'
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
	 * @param Zend_Controller_Request_Abstract $request The request object.
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
		} else {
			$translator = new Ztal_Tal_MockTranslator($this->_options);
		}
		$translator->useDomain($request->getControllerName());
		$view->getEngine()->setTranslator($translator);
		
		// We configure the view renderer in order to use our PHPTAL view
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
				
		// If the view template suffix has not already been changed away from
		// the Zend 'phtml' convention then change it to 'xhtml'.
		// This is done to separate the Ztal templates which are xml based
		// from any legacy phtml files which have native php code in them and
		// make porting of legacy Zend projects easier.

		
// TODO: Discuss, whether all scripts should be renamed.
// 		 (as suggested from Ztal)

// 		if ($viewRenderer->getViewSuffix() == 'phtml') {
// 			$viewRenderer->setViewSuffix('xhtml');
// 		}
// 		if ($view->layout()->getViewSuffix() == 'phtml') {
// 			$view->layout()->setViewSuffix('xhtml');
// 		}
		$viewRenderer->setView($view);
		Zend_Registry::set('Ztal_View', $view);
	}
}