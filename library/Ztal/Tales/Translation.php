<?php
/**
 * Tales namespace handler to allow definition of plurals in a translation.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Tales namespace handler to allow definition of plurals in a translation.
 *
 * This class should never be subclassed. It is simply a container for the
 * various tales routines.
 * Creates a namespace for the tales extensions.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
final class Ztal_Tales_Translation implements PHPTAL_Tales
{

	/**
	 * Tal extension to allow string casing.
	 *
	 * Example use within template:
	 * <span i18n:translate="Ztal_Tales_Translation.plural:string:singularKey,string:pluralKey,countVariable />
	 *
	 * @param string $src     The original string from the source template.
	 * @param bool   $nothrow Whether to throw an exception on error or not.
	 *
	 * @return string
	 */

	public static function plural($src, $nothrow)
	{
		$parts = explode(',', $src);
		$count = array_pop($parts);
		$outputParts = array();
		foreach ($parts as $currentPart) {
			$outputPart = str_replace("'", '', phptal_tale($currentPart, $nothrow));
			if ($outputPart[0] != '$') {
				$outputPart = "'" . $outputPart . "'";
			}
			$outputParts[] = $outputPart;
		}
		return 'array(\'pluralKeys\'=>array(' . implode(',', $outputParts)
			. '), \'count\'=>' . phptal_tale($count, $nothrow)
			. ', \'ctx\'=>$ctx)';
	}
	
	
	/**
	 * Tal extension to translate the values in an array.
	 *
	 * This Tal accepts an array and, via its helper function, returns a new
	 * array with identical keys but with values translated using the current
	 * translator.
	 *
	 * This Tal is most useful in translating an array before sorting it based
	 * on the translated content. Note that variable replacement in the post-
	 * translated string should still work but plurals won't.
	 *
	 * Example use within template:
	 * <span
	 *    tal:define="translatedArray Ztal_Tales_Translation.translateArrayValues:originalArray"
	 *    tal:repeat="item translatedArray"
	 *    tal:content="item"
	 * />
	 *
	 * @param string $src     The original string from the source template.
	 * @param bool   $nothrow Whether to throw an exception on error or not.
	 *
	 * @return string
	 */
	public static function translateArrayValues($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		
		return 'Ztal_Tales_Translation::arrayTranslationHelper('
			. phptal_tale($src, $nothrow) . ', $_translator)';
	}
	
	/**
	 * Helper for the translateArrayValues Tal.
	 *
	 * This helper does the runtime translation of the supplied array
	 * and returns a new array with the translated values.
	 *
	 * @param array                     $array      The array for translating.
	 * @param PHPTAL_TranslationService $translator The translator object.
	 *
	 * @return array
	 */
	public static function arrayTranslationHelper($array, $translator)
	{
		if (!is_array($array)) {
			return $array;
		}
		
		$results = array();
		foreach ($array as $key => $value) {
			$results[$key] = $translator->translate($value, false);
		}
		return $results;
	}
}