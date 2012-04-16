<?php
/**
 * Represent a column based on data from an object in an html table.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Represent a column based on data from an object in an html table.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Column_Object extends Ztal_Table_Column_Abstract
{
	/**
	 * Sort the supplied data source.
	 *
	 * @param mixed  &$dataSource   The data source to sort.
	 * @param string $sortField     The sort field specified for this column.
	 * @param int    $sortDirection The direction to sort in.
	 *
	 * @return void
	 */
	protected function _sortDataSource(&$dataSource, $sortField, $sortDirection)
	{
		/**
		 * Closure to handle the inversion of the sort results.
		 *
		 * When a descending sort is needed, the closure will invert
		 * the sort comparison function output to invert the sort directon.
		 *
		 * @param mixed $a The first item to compare.
		 * @param mixed $b The second item to compare.
		 *
		 * @return int
		 */
		usort($dataSource, function($a, $b) use ($sortField, $sortDirection)
			{
				$result = $a->$sortField($b);
				if ($sortDirection == Ztal_Table_Column_Abstract::DIRECTION_DESCENDING) {
					return ($result == 0 ? 0 : -$result);
				} else {
					return $result;
				}
			}
		);


	}
	
	/**
	 * Method to return the value for a key from the data source.
	 *
	 * @return mixed
	 */
	protected function _dataForKey($dataSource, $key)
	{		
		if (method_exists($dataSource, $key)) {
			return $dataSource->$key();
		}
		return '';
	}
}