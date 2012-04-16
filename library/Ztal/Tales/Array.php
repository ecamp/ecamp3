<?php
/**
 * PHPTal Tale Modifiers.
 *
 * A collection of extensions to PHPTal that provide useful array handling
 * routines within a template.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Creates a namespace for the tales extensions.
 *
 * This class should never be subclassed. It is simply a container for the
 * various tales routines.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

final class Ztal_Tales_Array implements PHPTAL_Tales
{
	/**
	 * Tal to support sorting of an array.
	 *
	 * Example usage:
	 * <span tal:define="sortedArray Ztal_Tales_Array.sort:sortMode,array" />
	 *
	 * sortMode may be:
	 *    regular      - standard sort with no type conversion
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
	public static function sort($src, $nothrow)
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

		return 'Ztal_Tales_Array::sortHelper('
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
	public static function sortHelper($array, $sortMode)
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
	 *  tal:condition="Ztal_Tales_Array.in:needle,haystack"
	 *  tal:content="MATCH"
	 * />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function in($src, $nothrow)
	{
		$break = strpos($src, ',');
		$needle = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$haystack = $src;
			$rest = 'NULL';
		} else {
			$haystack = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}

		return '(in_array(' . phptal_tale($needle, $nothrow) . ', '
			. phptal_tale($haystack, $nothrow) . ') ? 1 : '
			. phptal_tale($rest, $nothrow) . ')';
	}


	/**
	 * Tal for doing PHP's array intersect.
	 *
	 * Return an array of all the items that are in array1 and array2.
	 *
	 * Example usage:
	 * <span tal:define="diff Ztal_Tales_Array.intersect:mode,array1,array2" />
	 *
	 * mode may be:
	 *  key - diff on keys
	 *  value - diff on values
	 *
	 * See the php array_diff and array_diff_key functions for more details
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function intersect($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		$parts = explode(',', $src);

		return '(' . phptal_tale($parts[0], $nothrow)
			. ' == \'key\' ? array_intersect_key(' . phptal_tale($parts[1], $nothrow)
			. ',' . phptal_tale($parts[2], $nothrow) . ') : array_intersect('
			. phptal_tale($parts[1], $nothrow) . ','
			. phptal_tale($parts[2], $nothrow) . '))';
	}


	/**
	 * Tal for doing PHP's array diff.
	 *
	 * Return an array of the items in array1 that are not in array2.
	 *
	 * Example usage:
	 * <span tal:define="complement Ztal_Tales_Array.complement:mode,array1,array2" />
	 *
	 * mode may be:
	 *  key - diff on keys
	 *  value - diff on values
	 *
	 * See the php array_diff and array_diff_key functions for more details
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function complement($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		$parts = explode(',', $src);

		return '(' . phptal_tale($parts[0], $nothrow)
			. ' == \'key\' ? array_diff_key(' . phptal_tale($parts[1], $nothrow)
			. ',' . phptal_tale($parts[2], $nothrow) . ') : array_diff('
			. phptal_tale($parts[1], $nothrow) . ','
			. phptal_tale($parts[2], $nothrow) . '))';
	}

	/**
	 * Tal for doing PHP's array merging.
	 *
	 * Return an array of the items in array1 unioned with the items in array2.
	 *
	 * Note that if key mode is used, the php + operator is applied to merge
	 * the arrays (which prefers array1's value for a key that exists in both
	 * arrays) but the values are not compared and filtered for uniqueness.
	 * If value mode is selected, keys are discarded and the arrays merged
	 * using array_merge before filtered for uniqueness with array_unique.
	 *
	 * Example usage:
	 * <span tal:define="complement Ztal_Tales_Array.complement:mode,array1,array2" />
	 *
	 * mode may be:
	 *  key - diff on keys
	 *  value - diff on values
	 *
	 * See the php array_diff and array_diff_key functions for more details
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function union($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		$parts = explode(',', $src);

		return '(' . phptal_tale($parts[0], $nothrow) . ' == \'key\' ? ('
			. phptal_tale($parts[1], $nothrow) . '+'
			. phptal_tale($parts[2], $nothrow)
			. ')) : array_unique(array_values('
			. phptal_tale($parts[1], $nothrow) . '), array_values('
			. phptal_tale($parts[2], $nothrow) . ')))';
	}

}
