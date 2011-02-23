<?php
/**
 * PHPTal Tale Modifiers.
 *
 * A collection of extensions to PHPTal that provide useful additional variable handling routines within a template.
 *
 * @category  Namesco
 * @package   PHPTal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Creates a namespace for the tales extensions by clustering them as static methods on the class.
 *
 * This class should never be subclassed. It is simply a container for the various tales routines.
 *
 * @category Namesco
 * @package  PHPTal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

final class Ztal_Tales_Generic implements PHPTAL_Tales
{

	/**
	 * Tal extension to allow string casing.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.uc:option,variable" />
	 * Options:
	 *		first - uppercase the first letter of the string
	 *		word - uppercase the first letter of each word
	 *		all - uppercase the whole string
	 *		none - force lowercase on the whole string.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function uc($src, $nothrow)
	{
		$break = strpos($src, ',');
		$command = strtolower(substr($src, 0, $break));
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$string = $src;
			$rest = 'NULL';
		} else {
			$string = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		switch ($command) {

			case 'word':
				return 'ucwords(' . phptal_tale($src, $nothrow) . ')';
				break;

			case 'all':
				return 'strtoupper(' . phptal_tale($src, $nothrow) . ')';
				break;

			case 'none':
				return 'strtolower(' . phptal_tale($src, $nothrow) . ')';
				break;

			case 'first':
			default:
				return 'ucfirst(' . phptal_tale($src, $nothrow) . ')';
				break;
		}
		return phptal_tales($rest, $nothrow);
	}


	/**
	 * Tal extension to allow string replacement.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.strReplace:string,original,replacement" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function strReplace($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		$parts = explode(',', $src);
		return 'str_replace(' . phptal_tale($parts[1], $nothrow) . ', ' . phptal_tale($parts[2], $nothrow)
			. ', ' . phptal_tale($parts[0], $nothrow) . ')';
	}


	/**
	 * Tal extension to build a data structure out of a json string.
	 *
	 * Example use within template: <span tal:define=" myVar Ztal_Tales_Generic.fromJsonString:
	 * {'name':'robert','gender':'male'}" />
	 * Note that single rather than double quotes are used to wrap strings and these are auto-converted.
	 * In order to insert a single quote into the string content, use 2 single quotes together ('').
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function fromJsonString($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break === false) {
			$jsonString = $src;
		} else {
			$jsonString = substr($src, 0, $break);
		}

		$jsonString = str_replace(array("'", '""'), array('"',"\'"), $jsonString);
		return 'json_decode(' . phptal_tale($jsonString, $nothrow) . ', true)';
	}



	/**
	 * Tal extension to return the php type of a variable.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.phpType:variable" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function phpType($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return 'Ztal_Tales_Generic::runtimeCalculateType(' . phptal_tale($src, $nothrow) . ')';
	}


	/**
	 * Used by the phpType tal extension, returns a string of the type of the supplied variable.
	 *
	 * Should NEVER be called directly.
	 *
	 * This method is used rather than gettype directly because gettype is not guaranteed to return the
	 * same string values in future. This function can be updated to handle that possibility.
	 *
	 * @param mixed $var The var to calculate the type for.
	 *
	 * @return string
	 */
	public static function runtimeCalculateType($var)
	{
		return gettype($var);
	}


	/**
	 * Tal extension to return the result of a mod b (a%b in php talk).
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.mod:a,b" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function mod($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
		} else {
			$b = substr($src, 0, $break);
		}
		return '(' . phptal_tale($a, $nothrow) . '%' . phptal_tale($b, $nothrow) . ')';
	}



	/**
	 * Tal extension to return true when both arguments are equal.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.equal:a,b" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function equal($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(' . phptal_tale($a, $nothrow) . '==' . phptal_tale($b, $nothrow) . '?1:'
			. phptal_tale($rest, $nothrow) . ')';
	}

	/**
	 * Tal extension to return true when the first argument is greater.
	 *
	 * Example use within template:
	 *	<span tal:content="Ztal_Tales_Generic.greaterThan:a,b" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function greaterThan($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(' . phptal_tale($a, $nothrow) . '>'
			. phptal_tale($b, $nothrow) . ' ? true : false)';
	}


	/**
	 * Tal extension to return the supplied string when the value is true.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.isTrue:variable,string" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isTrue($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$string = $src;
			$notTrue = 'NULL';
		} else {
			$string = substr($src, 0, $break);
			$notTrue = phptal_tale(substr($src, $break + 1), $nothrow);
		}
		return '(' . phptal_tale($variable, $nothrow) . '==true?' . phptal_tale($string, $nothrow)
			. ':' . $notTrue . ')';
	}



	/**
	 * Tal extension to handle Zend_Date objects.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendDate:variable,format" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function zendDate($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$format = $src;
		} else {
			$format = substr($src, 0, $break);
		}
		return phptal_tale($variable, $nothrow) . '->toString("' . $format . '")';
	}


	/**
	 * Tal extension to handle formatting of numbers using a supplied Zend_Locale object.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendLocaleNumber:variable,localeObject" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function zendLocaleNumber($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$locale = $src;
		} else {
			$locale = substr($src, 0, $break);
		}
		return 'Zend_Locale_Format::toNumber((' . phptal_tale($variable, $nothrow) . '==\'\'?0:'
			. phptal_tale($variable, $nothrow) . '), array(\'locale\' => ' . phptal_tale($locale, $nothrow) . '))';
	}


	/**
	 * Tal extension to handle formatting of numbers using a supplied Zend_Currency object.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendCurrency:variable,currencyObject" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function zendCurrency($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$currency = $src;
		} else {
			$currency = substr($src, 0, $break);
		}
		return phptal_tale($currency, $nothrow) . '->toCurrency(' . phptal_tale($variable, $nothrow) . ')';
	}


	/**
	 * Formats a number to a certain decimal place.
	 *
	 * Formats the number to the provided decimal place - that's numberwang!
	 * Example use within template:
	 *
	 * <span
	 *   tal:content="Ztal_Tales_Generic.numberFormatDecimal:numberVar,string:2"
	 * />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function numberFormatDecimal($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$decimal = $src;
		} else {
			$decimal = substr($src, 0, $break);
		}

		return 'number_format(' . phptal_tale($variable, $nothrow)
			. ', ' . phptal_tale($decimal, $nothrow) . ')';
	}


	/**
	 * Tal to support sorting of an array.
	 *
	 * Example usage:
	 * <span tal:define="sortedArray Ztal_Tales_Generic.arraySort:sortMode,array" />
	 *
	 * sortMode may be:
	 *    regular      - standard sort with no type conversion during value comparison
	 *    string       - sort by comparing values as strings
	 *    numeric      - sort by comparing values as numbers
	 *    localeString - string sort using the current locale
	 *    natural      - sort using a natural sorting algorithm (see natsort())
	 *    none         - don't sort, just pass back the array
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function arraySort($src, $nothrow)
	{
		$break = strpos($src, ',');
		$command = strtolower(substr($src, 0, $break));
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$variable = $src;
			$rest = 'NULL';
		} else {
			$variable = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}

		return 'Ztal_Tales_Generic::arraySortHelper('
			. phptal_tale($variable, $nothrow) . ', '
			. phptal_tale($command, $nothrow) . ')';
	}

	/**
	 * Tal helper method for creating a sorted copy of the supplied array.
	 *
	 * @param array  $array    The array to copy and sort.
	 * @param string $sortMode The sort method to use.
	 *
	 * @return array
	 */
	public static function arraySortHelper($array, $sortMode)
	{
		if ($sortMode == 'none') {
			return $array;
		}

		$resultArray = $array;
		switch (strtolower($sortMode)) {
			case 'string':
				asort($resultArray, SORT_STRING);
				break;

			case 'numeric':
				asort($resultArray, SORT_NUMERIC);
				break;

			case 'localestring':
				asort($resultArray, SORT_LOCALE_STRING);
				break;

			case 'natural':
				natsort($resultArray);
				break;

			case 'regular':
			default:
				asort($resultArray, SORT_REGULAR);
				break;
		}
		return $resultArray;
	}

	/**
	 * Tal for doing PHP's in_array.
	 *
	 * Example usage:
	 *
	 * <span
	 *  tal:define="haystack string:val1,val2,val3"
	 *  tal:condition="Ztal_Tales_Generic.inArray:needle,haystack
	 *  tal:content="MATCH"
	 * />
	 *
	 * The heystack can be an existing array passed to the view from Zend, or it
	 * can be defined inline; if defining inline there is currently a limitation
	 * in that the values cannot contain a comma (currently used to explode).
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function inArray($src, $nothrow)
	{
		$regex = "/([a-zA-Z:]+)\s*,\s*([a-zA-Z:]+)$/";
		if (!preg_match($regex, $src, $items)) {
			return phptal_tales('NULL', $nothrow);
		}

		$heystack = phptal_tale($items[2], $nothrow);

		return "in_array(
			" . phptal_tale($items[1], $nothrow) . ",
			(is_array(" . $heystack . ") ? " . $heystack . " : array_map(
				'trim', explode(',', " . $heystack . ")
			))
		)";
	}


	/**
	 * Tal to support exclude filtering of an array.
	 *
	 * Example usage:
	 *
	 * <span
	 *  tal:define="keys string:key1,key2"
	 *  tal:repeat="arr Ztal_Tales_Generic.arrayExclude:string:mode,keys,array
	 * />
	 *
	 * mode may be:
	 *    key   - Filter items by the array key.
	 *    value - Filter items by the array value.
	 *
	 * The last parameter is the original array to filter, this should be a
	 * PHPTAL variable.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function arrayExclude($src, $nothrow)
	{
		$regex = "/([a-zA-Z:]+)\s*?,\s*?([a-zA-Z0-9:]+)\s*?,\s*?([^|$]+)$/";
		$src = trim($src);

		// If we can't find a match for our parameters simply return NULL.
		if (!preg_match($regex, $src, $items)) {
			return phptal_tales('NULL', $nothrow);
		}

		// Call the array filtering helper with:
		//
		// $items[1] = Type of filtering (e.g. key or value).
		// $items[2] = PHPTAL variable (array) of items to filter with, or a
		//             string of comma seperated items,
		// $items[3] = PHPTAL variable of haystack array.
		// true = Exclude rather than filter.
		return "Ztal_Tales_Generic::arrayFilterHelper(

			" . phptal_tale($items[1], $nothrow) . ",
			" . phptal_tale($items[2], $nothrow) . ",
			" . phptal_tale($items[3], $nothrow) . ",

			true)";

	}


	/**
	 * Tal to support filtering of an array.
	 *
	 * Example usage:
	 *
	 * <span
	 *  tal:define="keys string:key1,key2"
	 *  tal:repeat="arr Ztal_Tales_Generic.arrayFilter:string:mode,keys,array
	 * />
	 *
	 * mode may be:
	 *    key   - Filter items by the array key.
	 *    value - Filter items by the array value.
	 *
	 * The last parameter is the original array to filter, this should be a
	 * PHPTAL variable.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function arrayFilter($src, $nothrow)
	{
		$regex = "/([a-zA-Z:]+)\s*?,\s*?([a-zA-Z0-9:]+)\s*?,\s*?([^|$]+)$/";
		$src = trim($src);

		// If we can't find a match for our parameters simply return NULL.
		if (!preg_match($regex, $src, $items)) {
			return phptal_tales('NULL', $nothrow);
		}

		// Call the array filtering helper with:
		//
		// $items[1] = Type of filtering (e.g. key or value).
		// $items[2] = PHPTAL variable (array) of items to filter with, or a
		//             string of comma seperated items,
		// $items[3] = PHPTAL variable of haystack array.
		// true = Exclude rather than filter.
		return "Ztal_Tales_Generic::arrayFilterHelper(

			" . phptal_tale($items[1], $nothrow) . ",
			" . phptal_tale($items[2], $nothrow) . ",
			" . phptal_tale($items[3], $nothrow) . ")";

	}

	/**
	 * Helper for array filtering.
	 *
	 * This method filters the array by the filter array, based on either a key
	 * filter or value. If the exclude option is true it'll return the original
	 * array with the filtered items removed.
	 *
	 * @param string  $for      The mode to use, key or value.
	 * @param array   $filter   The array of keys, or values, to filter with.
	 * @param array   $original The original array to filter.
	 * @param boolean $exclude  Excludes the filtered results.
	 *
	 * @return array
	 */
	public static function arrayFilterHelper(
		$for, $filter, $original, $exclude=false
	) {
		$for = strtolower($for);
		$newArray = array();


		if (!is_array($filter)) {
			// Explode the filters by command.
			$filter = array_map('trim', explode(',', $filter));
		}

		if ($for == 'key') {
			foreach ($original as $k => $v) {
				if (in_array($k, $filter)) {
					$newArray[$k] = $v;
				}
			}
		} else {
			foreach ($original as $k => $v) {
				if (in_array($v, $filter)) {
					$newArray[$k] = $v;
				}
			}
		}

		// If we're excluding then we need to return the original array with the
		// newArray keys taken away.
		if ($exclude) {
			$keys = array_keys($newArray);
			$newArray = array();
			foreach ($original as $k => $v) {
				if (!in_array($k, $keys)) {
					$newArray[$k] = $v;
				}
			}
		}

		return $newArray;
	}
}
