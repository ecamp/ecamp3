<?php
/**
 * PHPTal Zend_Translate translation plugin.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPTal Zend_Translate translation plugin.
 *
 * A PHPTal translator compatible subclass that uses Zend_Translate.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Tal_ZendTranslateTranslator implements PHPTAL_TranslationService
{
	/**
	 * The translation domain to use. A translation domain is akin to a namespace.
	 *
	 * @var string
	 */
	protected $_domain;

	/**
	 * Whether to mark failed translations with a '**' prefix.
	 *
	 * @var bool
	 */	 
	protected $_highlightFailedTranslations;


	/**
	 * Constructor.
	 *
	 * @param array $options Configuration options.
	 */
	public function __construct($options = array())
	{
		// if specified, set to highlight failed translations
		if (isset($options['highlightFailedTranslations'])) {
			$this->setHighlightFailedTranslations(($options['highlightFailedTranslations'] == '1'));
		}
	}

	
	/**
	 * Set whether failed translation strings should be prefixed with a '**'.
	 *
	 * @param bool $flag Whether to prefix failed translation strings.
	 *
	 * @return void
	 */
	public function setHighlightFailedTranslations($flag)
	{
		$this->_highlightFailedTranslations = $flag;
	}
	
	/**
	 * Set the target language for translations.
	 *
	 * Only the first passed language is actually used due to Zend's locale support
	 *
	 * @return string - chosen language
	 */
	public function setLanguage(/*...*/)
	{
		$translator = Zend_Registry::get('Zend_Translate');
		return $translator->setLocale(array_shift(func_get_args()));
	}
	
	/**
	 * Set the encoding used for translated output.
	 *
	 * PHPTAL will inform translation service what encoding the page uses.
	 * Output of translate() must be in this encoding.
	 *
	 * Ignored when used with the Zend integration as encoding is handled separately.
	 *
	 * @param string $encoding The page encoding in use. Ignored and set separately within Zend.
	 *
	 * @return void
	 */
	public function setEncoding($encoding)
	{
	}
	
	/**
	 * Set the domain to use for translations.
	 *
	 * A translation domain is akin to a coding namespace and is referred to as the Context in gettext docs.
	 *
	 * @param string $domain The translation domain to use.
	 *
	 * @return void
	 */
	public function useDomain($domain)
	{
		$this->_domain = trim($domain);
	}
	
	/**
	 * Set an interpolation var.
	 *
	 * An interpolation var is a key/value pair that matches with and replaces ${key} tokens in the template.
	 *
	 * @param string $key   The name of the token to replace. Represented by ${key} in the template
	 *                      and translation strings.
	 * @param string $value The value to replace the token with.
	 *
	 * @return void
	 */
	public function setVar($key, $value)
	{
		$this->_vars[$key] = $value;
	}
	
	/**
	 * Translate a given key string.
	 *
	 * Look up the translation for the supplied key string. first try in the current domain, then try in the
	 * global domain. Also handles plural translations and replaces the key with the correct value translation
	 * for the given count value.
	 *
	 * If no translation is found, the original key is returned, optionally prefixed by "**".
	 *
	 * Once translated, the result string is then key/value substituted so as to fill in placeholders.
	 *
	 * @param string $key        The key to translate.
	 * @param bool   $htmlencode If true, output will be HTML-escaped.
	 *
	 * @return string
	 */
	public function translate($key, $htmlencode = true)
	{
		$translator = Zend_Registry::get('Zend_Translate');
		
		//is the translation is a plurals translation created using the 'plurals' modifier?
		if (is_array($key) && isset($key['pluralKeys'])) {
			//grab the global translation key array
			$globalTranslateSet = $key['pluralKeys'];
			
			//create the domain specific translation array by prepending the domain key
			// and trim the global key while we are at it
			$domainTranslateSet = array();
			foreach ($globalTranslateSet as &$currentKey) {
				$currentKey = trim($currentKey);
				$domainTranslateSet[] = $this->_domain . chr(4) . $currentKey;
			}
			
			//work out the count variable value
			$countVariable = $key['count'];
			
			//append the count value to the end of the arrays
			$domainTranslateSet[] = $countVariable;
			$globalTranslateSet[] = $countVariable;
			
			//register 'count' as a post-translation var so the count can appear in the translated message
			if (! isset($this->_vars)) {
				$this->_vars = array();
			}
			$this->_vars['count'] = $countVariable;
			
			//check first if a domain specific translation is available
			if ($translator->isTranslated($domainTranslateSet[0])) {
				$value = $translator->translate($domainTranslateSet);

				//then check if a global translation is available
			} elseif ($translator->isTranslated($globalTranslateSet[0])) {
				$value = $translator->translate($globalTranslateSet);
				
				//otherwise just return the first key plus the count info
			} else {
				$value = $globalTranslateSet[0] . '[' . $countVariable . ']';
				if ($this->_highlightFailedTranslations) {
					$value = '**' . $value;
				}
			}
			
			//otherwise the translation is a simple value
		} else {
			$key = trim($key);
			if ($translator->isTranslated($this->_domain . chr(4) . $key)) {
				$value = $translator->translate($this->_domain . chr(4) . $key);
			} elseif ($translator->isTranslated($key)) {
				$value = $translator->translate($key);
			} else {
				$value = $key;
				if ($this->_highlightFailedTranslations) {
					$value = '**' . $value;
				}
			}
		}
		
		//if requested, html-encode the returned value
		if ($htmlencode) {
			$value = htmlspecialchars($value, ENT_QUOTES);
		}

		//replace any registered post-translation vars
		while (preg_match('/\${(.*?)\}/sm', $value, $m)) {
			list($src, $var) = $m;
			if (! isset($this->_vars[$var])) {
				throw new PHPTAL_VariableNotFoundException('Interpolation error. Translation uses ${'
					. $var . '}, which is not defined in the template (via i18n:name)');
			}
			$value = str_replace($src, $this->_vars[$var], $value);
		}
		return $value;
	}
}