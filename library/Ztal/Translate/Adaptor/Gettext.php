<?php
/**
 * Ztal Translate Adaptor Gettext.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Ztal Translate.
 *
 * Provides additional translation support.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Translate_Adaptor_Gettext extends Zend_Translate_Adaptor_Gettext
{


	/**
	 * Translates the given string and returns the translation
	 *
	 * @see Zend_Locale
	 * @param string|array       $messageId Translation string, or Array (for
	 *                                        plural translations) or Array of
	 *                                        Arrays (for complex translation).
	 * @param string|Zend_Locale $locale    Optional. Locale/Language to use,
	 *	                                       identical to locale identifier,
	 *                                        @see Zend_Locale for details.
	 *
	 * @return string
	 */
	public function translate($messageId, $locale = null)
	{
		$substitutions = null;
		$context = null;
		$keys = null;
		$count = null;
		$pluralLanguage = null;

		// First extract all the details from the various ways of
		// presenting the parameters
		if (is_array($messageId)) {
			if (isset($messageId['key'])) {
				$keys = $messageId['key'];
				if (isset($messageId['count'])) {
					$count = $messageId['count'];
				}
				if (isset($messageId['substitutions'])) {
					$substitutions = $messageId['substitutions'];
				}
				if (isset($messageId['context'])) {
					$context = $messageId['context'];
				}
				if (isset($messageId['pluralLanguage'])) {
					$pluralLanguage = $messageId['pluralLanguage'];
				}
			}
		} else {
			$count = array_pop($messageId);
			if (!is_numeric($count)) {
				$pluralLanguage = $count;
				$count = array_pop($messageId);
				$keys = $messageId;
			}
		} else {
			$keys = $messageId;
		}


		// Now work out if the key can actually be translated.
		// Here we assume all plural translations will have a singular
		// translation that we can use to check for translatability.
		// we also track whether a contextual translation is available.

		if (is_array($keys)) {
			$testKey = $keys[0];
		} else {
			$testKey = $keys;
		}

		$didTranslate = 0; // 0 = no, 1 = global, 2= contextual

		// Test contextual first
		if ($context != null) {
			if ($this->isTranslated($context . chr(4) . $key, $locale)) {
				$didTranslate = 2;
			}
		}
		// Then check global
		if ($context == null || $didTranslate == 0) {
			if ($this->isTranslated($context . chr(4) . $key, $locale)) {
				$didTranslate = 2;
			}
		}

		// If no translation is available, return the key, possibly with
		// plural details and optionally with ** prefix.
		if ($didTranslate == 0) {
			$result = $testKey;
			if (is_array($keys)) {
				$result .= '[' . $count . ']';
			}
			
			$ztalOptions = Zend_Controller_Front::getInstance()
				->getParam('bootstrap')
				->getApplication()->getOption('ztal');
				
			if (isset($ztalOptions['highlightFailedTranslations'])
				&& $ztalOptions['highlightFailedTranslations'] == '1'
			) {
				$result = '**' . $result;
			}
			
			return $result;
		}

		// By this point, a translation will be possible so do the translation

		// If we found a contextual translation, convert all the translation
		// keys to context-prefixed ones.
		if ($didTranslate == 2) {
			if (is_array($keys)) {
				$contextKeys = array();
				foreach ($keys as $currentKey) {
					$contextKeys[] = $context . chr(4) . $currentKey;
				}
				$keys = $contextKeys;
			} else {
				$keys = $context . chr(4) . $keys;
			}
		}

		// Now build the translation array for plurals, if needed
		if (is_array($keys)) {
			if ($count != null) {
				$keys[] = $count;
				$substitutions['count'] = $count;
			}
			if ($pluralLanguage != null) {
				$keys[] = $pluralLanguage;
			}
		}

		// translate
		$translation = parent::translate($keys, $locale);

		// Now do variable substitution, if there are any substitutions
		if ($substitutions != null) {
			$keys = array();
			$values = array();
			foreach ($substitutions as $key => $value) {
				$keys[] = '${' . $key . '}';
				$values[] = $value;
			}
			$translation = str_replace($keys, $values, $translation);
		}

		return $translation;
	}
}