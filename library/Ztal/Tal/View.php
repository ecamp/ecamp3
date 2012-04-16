<?php
/**
 * Zend View subclass that handles actual rendering of View content using PHPTal.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Subclass of Zend View that replaces the standard functionality with PHPTal.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Tal_View extends Zend_View
{

	/**
	 * The PHPTal engine.
	 *
	 * @var PHPTAL
	 */
	protected $_engine = null;

	/**
	 * Whether to flush the template cache before rendering.
	 *
	 * @var bool
	 */
	protected $_purgeCacheBeforeRender = false;

	/**
	 * Whether to turn on the whitespace compression filter.
	 *
	 * @var bool
	 */
	protected $_compressWhitespace = false;

	/**
	 * A Zend_Cache instance.
	 *
	 * @var Zend_Cache|false
	 */
	protected $_zendPageCache = false;

	/**
	 * The cache content from a zendCache instance.
	 *
	 * @var string|false
	 */
	protected $_zendPageCacheContent = false;

	/**
	 * How long the rendered page should be cached for.
	 *
	 * @var int|false
	 */
	protected $_zendPageCacheDuration = false;

	/**
	 * The key to use for the cache item.
	 *
	 * @var string|false
	 */
	protected $_zendPageCacheKey = false;



	/**
	 * Constructor.
	 *
	 * @param array $options Configuration options.
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		
		$this->setEngine(new PHPTAL());
		
		// configure the encoding
		if (isset($options['encoding']) && $options['encoding'] != '') {
			$this->setEncoding((string)$options['encoding']);
		} else {
			$this->setEncoding('UTF-8');
		}

		// change the compiled code destination if set in the config
		if (isset($options['cacheDirectory']) && $options['cacheDirectory'] != '') {
			$this->setCacheDirectory((string)$options['cacheDirectory']);
		}

		// configure the caching mode
		if (isset($options['cachePurgeMode'])) {
			$this->setCachePurgeMode($options['cachePurgeMode'] == '1');
		}
		
		// configure the whitespace compression mode
		if (isset($options['compressWhitespace'])) {
			$this->setCompressWhitespace($options['compressWhitespace'] == '1');
		}
		
		// Stack up the script paths. Zend's setScriptPath call is lifo
		// so we start with the bottom item first.
		
		// First set the path for Ztal's own macros
		$ztalBasePath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
		$this->setScriptPath($ztalBasePath . DIRECTORY_SEPARATOR . 'Macros');

		// Now setup the directories specified in the Ztal config
		if (isset($options['globalTemplatesDirectory'])) {
			$directories = $options['globalTemplatesDirectory'];
			if (!is_array($directories)) {
				$directories = array($directories);
			}
			foreach ($directories as $currentDirectory) {
				$this->addScriptPath($currentDirectory);
			}
		}
	
	
	
		// Next setup the custom modifiers
		
		//load in all php files that exist in the custom modifiers directory
		if (isset($options['customModifiersDirectory'])) {
			$customModifiers = $options['customModifiersDirectory'];
			if (!is_array($customModifiers)) {
				$customModifiers = array($customModifiers);
			}
			foreach ($customModifiers as $currentPath) {
				$this->addCustomModifiersPath($currentPath);
			}
		}

		// Add ZTal's tales repository as a final default.
		$ztalTalesPath = $ztalBasePath . DIRECTORY_SEPARATOR . 'Tales';
		$this->addCustomModifiersPath($ztalTalesPath);
	}
	 
	 
	 
	/**
	 * Load in all php files in the specified directory.
	 *
	 * @param string $customModifiersPath Path to scan for php files to load.
	 *
	 * @return void
	 */		
	public function addCustomModifiersPath($customModifiersPath)
	{
		if (is_dir($customModifiersPath)) {
			foreach (new DirectoryIterator($customModifiersPath) as $modifierFile) {
				if ($modifierFile->isDot()) {
					continue;
				}
				if ($modifierFile->isDir()) {
					$this->addCustomModifiersPath($modifierFile->getPathname());
					continue;
				} else {
					if (!preg_match('/^[^.].+\.php$/', $modifierFile->getFilename())) {
						continue;
					}
					include_once $modifierFile->getPathname();
				}
			}
		}
	}



	/**
	 * Handle cloning of the view by cloning the PHPTAL object correctly.
	 *
	 * @return void
	 */
	public function __clone()
	{
		$this->_engine = clone $this->_engine;
	}



	/**
	 * Changes the current PHPTAL instance.
	 *
	 * @param mixed $tal The engine to use (supplied to the Zend system by the Resource View).
	 *
	 * @return void
	 */
	public function setEngine($tal)
	{
		$this->_engine = $tal;
		$this->_engine->this = $this;
	}



	/**
	 * Returns the current PHPTAL instance.
	 *
	 * @return std_class
	 */
	public function getEngine()
	{
		return $this->_engine;
	}



	/**
	 * Changes the cache purge mode.
	 *
	 * @param bool $newValue Whether to delete old template cache files before rendering.
	 *
	 * @return void
	 */	  
	public function setCachePurgeMode($newValue)
	{
		$this->_purgeCacheBeforeRender = $newValue;
	}



	/**
	 * Sets the encoding used in outputting renders.
	 *
	 * @param string $encoding The encoding to use.
	 *
	 * @return void
	 */
	public function setEncoding($encoding)
	{
		parent::setEncoding($encoding);
		$this->_engine->setEncoding(parent::getEncoding());
	}



	/**
	 * Sets whether whitespace compression should be performed.
	 *
	 * @param bool $flag Whether to compress whitespace.
	 *
	 * @return void
	 */
	public function setCompressWhitespace($flag)
	{
		$this->_compressWhitespace = (bool)$flag;
	}



	/**
	 * Gets whether whitespace compression is currently turned on.
	 *
	 * @return bool
	 */
	public function getCompressWhitespace()
	{
		return $this->_compressWhitespace;
	}

	
	
	/**
	 * Set the directory used to save generated templates.
	 *
	 * @param string $path The path to use.
	 *
	 * @return void
	 */
	public function setCacheDirectory($path)
	{
		$this->_engine->setPhpCodeDestination($path);	
	}
	


	/**
	 * Whether untranslated strings should be highlighted by prepending '**'.
	 *
	 * @param bool $flag Whether to highlight failed translations.
	 *
	 * @return void
	 */
	public function setHighlightFailedTranslations($flag)
	{
		$translator = $this->_engine->getTranslator();
		if (is_object($translator)) {
			$translator->setHighlightFailedTranslations($flag);
		}
	}



	/**
	 * Returns the cache purge mode.
	 *
	 * @return bool
	 */
	public function getCachePurgeMode()
	{
		return $this->_purgeCacheBeforeRender;
	}


	/**
	 * Setup the parameters to cache the result of a page render.
	 *
	 * @param Zend_Cache        $cache   The cache to use.
	 * @param Zend_Config|array $options Additional options.
	 *
	 * @return bool
	 */
	public function cacheRenderedPage($cache, $options)
	{
		// If the options are a Zend_Config object, convert to an array
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}
		
		// If the lifetime has been configured use it else default it.
		if (isset($options['lifetime'])) {
			$this->_zendPageCacheDuration = $options['lifetime'];
		} else {
			$this->_zendPageCacheDuration = 1800; // half an hour
		}
		
		
		// If the key has been configured use it else default it.
		if (isset($options['key'])) {
			$this->_zendPageCacheKey = $options['key'];
		} else {
			$this->_zendPageCacheKey = $_SERVER['REQUEST_URI'];
		}
		
		// Prepend the key to prevent namespace collisions and
		// remove unsupported chars
		$this->_zendPageCacheKey = 'ZtalPage'
			. str_replace('/', '', $this->_zendPageCacheKey);
		
		// Store the cache object
		$this->_zendPageCache = $cache;
		
		// Check if the cache item exists and, if it does, fetch it
		$this->_zendPageCacheContent = $this->_zendPageCache->load(
			$this->_zendPageCacheKey);
		
		// return whether a valid cache item could be fetched.
		return ($this->_zendPageCacheContent != false);
	}
	
	
	
	
	/**
	 * Returns PHPTAL output - either from a render or from the cache.
	 *
	 * @param string|array $template The name of the template to render or
	 *                                an array with the ('src') src for a template
	 *                                and a ('name') name to help identify the
	 *                                template in error messages.
	 *
	 * @return string
	 */
	public function render($template)
	{
		// Check we are fully configured and initialised.
		if ($this->_engine == null) {
			throw new Zend_View_Exception('PHPTAL is not defined', $this);
		}


		// If a cache has been setup and content is available, return it
		if ($this->_zendPageCacheContent != false) {
			return $this->_zendPageCacheContent;
		}
		
		// Setup the script locations based on the view's script paths
		$this->_engine->setTemplateRepository($this->getScriptPaths());

		// Do this at this point rather than in the constructor because we don't
		// know what the template repositories are going to be at that point.
		$this->_engine->addSourceResolver(
			new Ztal_Tal_PharResolver($this->getScriptPaths()));

		// Assign all the variables set here through to the PHPTAL engine.
		foreach ($this->getVars() as $key => $value) {
			$this->_engine->set($key, $value);
		}
		
		
		if (!is_array($template)) {
			$this->_engine->setTemplate($this->_convertTemplateName($template));
		} else {
			$this->_engine->setSource($template['src'], $template['name']);
		}
		
		
		// Setup a collection of standard variable available in the view
		$this->_engine->set('doctype', $this->doctype());
		$this->_engine->set('headTitle', $this->headTitle());
		$this->_engine->set('headScript', $this->headScript());
		$this->_engine->set('headLink', $this->headLink());
		$this->_engine->set('headMeta', $this->headMeta());
		$this->_engine->set('headStyle', $this->headStyle());
		$this->productionMode = ('production' == APPLICATION_ENV);

		// If perging of the tal template cache is enabled
		// find all template cache files and delete them
		if ($this->_purgeCacheBeforeRender) {
			$cacheFolder = $this->_engine->getPhpCodeDestination();
			if (is_dir($cacheFolder)) {
				foreach (new DirectoryIterator($cacheFolder) as $cacheItem) {
					if (strncmp($cacheItem->getFilename(), 'tpl_', 4) != 0 || $cacheItem->isdir()) {
						continue;
					}
					@unlink($cacheItem->getPathname());
				}
			}
		}
		
		
		// if a layout is being used and nothing has already overloaded the viewContent,
		// register the content as viewContent, otherwise set it to empty
		if (!isset($this->viewContent)) {
			if ($this->getHelperPath('layout') != false && $this->layout()->isEnabled()) {
				$this->_engine->set('viewContent', $this->layout()->content);
			} else {
				$this->viewContent = '';
			}
		}
		
		// Strip html comments and compress un-needed whitespace
		$this->_engine->addPreFilter(new PHPTAL_PreFilter_StripComments());
		
		if ($this->_compressWhitespace == true) {
			$this->_engine->addPreFilter(new PHPTAL_PreFilter_Compress());
		}
		
		try {
			$result = $this->_engine->execute();
		} catch(PHPTAL_TemplateException $e) {
			// If the exception is a root PHPTAL_TemplateException
			// rather than a subclass of this exception and xdebug is enabled,
			// it will have already been picked up by xdebug, if enabled, and
			// should be shown like any other php error.
			// Any subclass of PHPTAL_TemplateException can be handled by
			// the phptal internal exception handler as it gives a useful
			// error output
			if (get_class($e) == 'PHPTAL_TemplateException'
				&& function_exists('xdebug_is_enabled')
				&& xdebug_is_enabled()
			) {
				exit();
			}
			throw $e;
		}
		
		
		// If the page needed to be rendered but was configured to
		// cache then cache the result of the render.
		if (($this->_zendPageCache instanceof Zend_Cache_Core)) {
			$this->_zendPageCache->save($result, $this->_zendPageCacheKey,
				array(), $this->_zendPageCacheDuration);
		}
		
		return $result;
	}



	/**
	 * Needed as a subclass of Zend_View but not used.
	 *
	 * @return void
	 */
	protected function _run()
	{
	}


	/**
	 * Convert the template name formatting.
	 *
	 * @param string $templateName The template name to convert.
	 *
	 * @return string
	 */
	protected function _convertTemplateName($templateName)
	{
		$templateParts = explode('-', $templateName);
		$firstPart = array_shift($templateParts);
		foreach ($templateParts as &$currentPart) {
			$currentPart = ucfirst($currentPart);
		}
		return $firstPart . implode('', $templateParts);
	}
}
