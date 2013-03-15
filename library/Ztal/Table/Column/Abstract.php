<?php
/**
 * Represent a column in an html table.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Represent a column in an html table.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Column_Abstract
{
	const DIRECTION_ASCENDING = 1;
	const DIRECTION_DESCENDING = 0;


	/**
	 * The key for the column.
	 *
	 * @var string
	 */
	 protected $_columnKey;
	 
	/**
	 * The key used to fetch this columns data from the data source.
	 *
	 * @var string
	 */
	protected $_dataKey;
	
	/**
	 * The name of the sort field.
	 *
	 * @var string|null
	 */
	protected $_sortField;

	/**
	 * The sort direction.
	 *
	 * @var int|null
	 */
	protected $_sortDirection;


	/**
	 * A function to format the data before handing it over to the table.
	 *
	 * @var function|null
	 */
	protected $_formatter;

	
	/**
	 * Constructor.
	 *
	 * @param string $columnKey    The identifier for the column.
	 * @param string $dataKey      The name of the dataSource for the column.
	 * @param array  $options      Optional config options.
	 */
	public function __construct($columnKey, $dataKey = null,
		array $options = array()
	) {
		$this->_columnKey = $columnKey;
		$this->_dataKey = $dataKey;

		if (isset($options['sortField']) && $options['sortField'] != '') {		
			if (isset($options['defaultSortDirection'])) {
				$this->_sortDirection = $options['defaultSortDirection'];
			} else {
				$this->_sortDirection = self::DIRECTION_ASCENDING;
			}
			$this->_sortField = $options['sortField'];
		} else {
			$this->_sortField = null;
		}
		
		if (isset($options['formatter']) && is_callable($options['formatter'])) {
			$this->_formatter = $options['formatter'];
		} else {
			$this->_formatter = null;
		}
	}
	
	
	/**
	 * Return the data for the column from the supplied dataSource.
	 *
	 * @param mixed $dataSource The data source.
	 *
	 * @return string
	 */
	public function getColumnDataForSource($dataSource)
	{
		$data = $this->_dataForKey($dataSource, $this->_dataKey);
		
		if ($this->_formatter != null) {
			$formatter = $this->_formatter;
			$data = $formatter($data);
		}
		return $data;
	}
	
	
	/**
	 * Return the key for the column.
	 *
	 * @return string
	 */
	public function getColumnKey()
	{
		return $this->_columnKey;
	}
	
	/**
	 * Can the current column be sorted.
	 *
	 * @return bool
	 */
	public function isSortable()
	{
		return $this->_sortField != null;
	}
	
	/**
	 * Return the sort field for the column.
	 *
	 * @return string
	 */
	public function getSortField()
	{
		return $this->_sortField;
	}

	/**
	 * Return the sort direction for the column.
	 *
	 * @return int
	 */
	public function getSortDirection()
	{
		return $this->_sortDirection;
	}
	
	
	/**
	 * Set the sort direction for the current column.
	 *
	 * @param int $direction The sort direction.
	 *
	 * @return void
	 */
	public function setSortDirection($direction)
	{
		if ($direction == self::DIRECTION_DESCENDING) {
			$this->_sortDirection = self::DIRECTION_DESCENDING;
		} else {
			$this->_sortDirection = self::DIRECTION_ASCENDING;
		}
	}
	
	
	/**
	 * Sort the data source by sortField in sortDirection.
	 *
	 * @param mixed &$dataSource The data source to sort.
	 *
	 * @return void
	 */
	public function sort(&$dataSource)
	{
		$this->_sortDataSource($dataSource, $this->_sortField,
			$this->_sortDirection);
	}
	
	
	/**
	 * Subclassable method to sort the supplied data source.
	 *
	 * @return void
	 */
	protected function _sortDataSource(&$dataSource, $sortField, $sortDirection)
	{
		throw new Exception('Invalid call to method in Abstract class');
	}
	
	/**
	 * Subclassable method to return the value for a key from the data source.
	 *
	 * @return mixed
	 */
	protected function _dataForKey($dataSource, $key)
	{
		throw new Exception('Invalid call to method in Abstract class');
	}
	
}