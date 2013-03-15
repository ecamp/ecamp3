<?php
/**
 * PHPTal Zend_Translate mock translation plugin.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPTal Zend_Translate mock translation plugin.
 *
 * A PHPTal translator compatible subclass that does absolutely no translation.
 * This class is used when no translation is configured within Zend and prevents
 * the translation-enabled macros / templates to work without complaining.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Tal_MockTranslator implements PHPTAL_TranslationService
{
	/**
	 * Constructor.
	 *
	 * @param array $options Configuration options.
	 */
	public function __construct($options = array())
	{
	}

		
	/**
	 * Set the target language for translations.
	 *
	 * Only the first passed language is actually used due to Zend's locale support
	 *
	 * @return void
	 */
	public function setLanguage(/*...*/)
	{
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
		//is the translation is a plurals translation created using the 'plurals' modifier?
		if (is_array($key) && isset($key['pluralKeys'])) {
			$value = trim($key['pluralKeys'][0]) . '[' . $key['count'] . ']';
			
			//otherwise the translation is a simple value
		} else {
			$value = trim($key);
		}
		
		//if requested, html-encode the returned value
		if ($htmlencode) {
			$value = htmlspecialchars($value, ENT_QUOTES);
		}

		return $value;
	}
}