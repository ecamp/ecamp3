<?php
/**
 * Class to handle array pagination in html tables.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Class to handle array pagination in html tables.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Paginator_Array extends Ztal_Table_Paginator_Abstract
{	
	/**
	 * Perform a slice on the data source.
	 *
	 * @param mixed &$dataSource The data source.
	 * @param int   $start       The first item's index in the slice.
	 * @param int   $count       The number of items in the slice.
	 *
	 * @return void
	 */
	protected function _sliceDataSource(&$dataSource, $start, $count)
	{
		$dataSource = array_slice($dataSource, $start, $count);
	}

}