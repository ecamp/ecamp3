<?php
/**
 * Abstract class to handle html tables.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Abstract class to handle html tables.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Abstract implements Countable, Iterator
{
	/**
	 * The Id of the table.
	 *
	 * @var string
	 */
	protected $_id;
	
	/**
	 * The data source for the table.
	 *
	 * Must support arrayAccess using sequential int keys.
	 *
	 * @var mixed
	 */
	protected $_dataSource;
	
	/**
	 * An array of column objects representing the table columns.
	 *
	 * @var array
	 */
	protected $_columns;


	/**
	 * An array of column indexes specifying the ordering of the columns.
	 *
	 * @var array
	 */
	protected $_columnKeyIndex;


	/**
	 * The shared Row Object.
	 *
	 * @var Ztal_Table_Row
	 */
	protected $_rowObject;
	
	/**
	 * Key of the current sort column.
	 *
	 * @var string
	 */
	protected $_sortColumnKey;
	
	
	/**
	 * Track the current row used in the iterator.
	 *
	 * @var int
	 */
	protected $_currentRowIndex;
	
	/**
	 * The URI for the action that handles the table.
	 *
	 * @var string
	 */
	protected $_baseUri;
	
	
	/**
	 * The table's pagination delegate, if any.
	 *
	 * @var Ztal_Table_Paginator_Abstract|null
	 */
	protected $_paginator;


	/**
	 * The constructor.
	 *
	 * @param array $parameters Optional. Params from a previous page usually
	 *                                     passed from the request object in the
	 *                                     controller.
	 */
	public function __construct(array $parameters = array())
	{		
		$this->_dataSource = null;
		$this->_currentRowIndex = 0;
		$this->_columns = array();
		$this->_columnKeyIndex = array();
		$this->_id = 'table';
		$this->_sortColumnKey = '';
		$this->_baseUri = '/';
		$this->_paginator = null;
		$this->_rowObject = new Ztal_Table_Row($this->_columns,
			$this->_columnKeyIndex);
		
		// Overrideable function to configure the columns etc.
		$this->_init();
	
		// The supplied parameters for sort and direction override the
		// defaults and anything done in init()
		$sortDirection = null;
		if (isset($parameters[$this->_id . '-sort'])) {
			$sortColumn = $parameters[$this->_id . '-sort'];
			if ($sortColumn != null && isset($this->_columns[$sortColumn])) {
				$this->_sortColumnKey = $sortColumn;
			}
			if (isset($parameters[$this->_id . '-direction'])) {
				$sortDirection = $parameters[$this->_id . '-direction'];
			} else {
				$sortDirection = null;
			}
		}
			
		if ($this->_sortColumnKey != ''
			&& isset($this->_columns[$this->_sortColumnKey])
			&& $sortDirection != null
		) {
			$this->_columns[$this->_sortColumnKey]->setSortDirection($sortDirection);
		}
		
		$paginator = $this->getPaginator();
		if ($paginator != null) {
			$paginator->initWithParameters($parameters, $this->_id . '-');
		}
	}
	
	



	/**
	 * Append a column to the column list.
	 *
	 * @param Ztal_Table_Column_Abstract $column The column.
	 *
	 * @return void
	 */
	public function appendColumn(Ztal_Table_Column_Abstract $column)
	{
		$this->_columns[$column->getColumnKey()] = $column;
		$this->_columnKeyIndex[] = $column->getColumnKey();
	}
	 

	/**
	 * Return the array of table columns and their capabilities.
	 *
	 * @return array
	 */
	public function getColumnDetails()
	{
		$results = array();
		foreach ($this->_columnKeyIndex as $currentColumnIndex) {
			$currentColumn = $this->_columns[$currentColumnIndex];
			$results[] = array(
				'key' => $currentColumn->getColumnKey(),
				'sortable' => $currentColumn->isSortable(),
				'sortDirection' => ($currentColumn->isSortable()
					? $currentColumn->getSortDirection() : 0 )
			);
		}
		return $results;
	}




	/**
	 * How many columns the table has.
	 *
	 * @return int
	 */
	public function columnCount()
	{
		return count($this->_columns);
	}
	



	// ** Countable **
	//********************************************
	
	/**
	 * Countable Interface. How many rows the table has.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->_dataSource);
	}
	

	
	// ** Iterator **
	//********************************************

	
	/**
	 * Iterator Interface. Returns the current value.
	 *
	 * @return Ztal_Table_Row The row object for the current row.
	 */
	public function current()
	{
		$this->_rowObject->setRowDataSource(
			$this->_dataSource[$this->_currentRowIndex]);
		return $this->_rowObject;
	}
	
	
	/**
	 * Iterator Interface. Returns the current key.
	 *
	 * @return mixed The current offset.
	 */
	public function key()
	{
		return $this->_currentRowIndex;
	}

	/**
	 * Iterator Interface. Increments the iterator and returns the new value.
	 *
	 * @return void
	 */	
	public function next()
	{
		$this->_currentRowIndex++;
	}
	
	/**
	 * Iterator Interface. Resets the iterator to the start of the sequence.
	 *
	 * @return void
	 */	
	public function rewind()
	{
		$this->_currentRowIndex = 0;
	}

	/**
	 * Iterator Interface. Checks if the current offset is valid.
	 *
	 * @return bool.
	 */	
	public function valid()
	{
		return ($this->_currentRowIndex >= 0
			&& $this->_currentRowIndex < count($this->_dataSource));
	}





	// ** Getters and Setters **
	//********************************************


	/**
	 * Return the base uri.
	 *
	 * @return string
	 */
	public function getBaseUri()
	{
		return $this->_baseUri;
	}
	/**
	 * Set the base uri.
	 *
	 * @param string $uri The new uri.
	 *
	 * @return void
	 */
	public function setBaseUri($uri)
	{
		$this->_baseUri = $uri;
	}






	/**
	 * Return the table's id.
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}
	/**
	 * Set the table's id.
	 *
	 * @param string $id The new id.
	 *
	 * @return void
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}




	/**
	 * Return the table's current sort column.
	 *
	 * @return Ztal_Table_Column_Abstract|null
	 */
	public function getSortColumn()
	{
		if ($this->_sortColumnKey == '') {
			return null;
		}
		return $this->_columns[$this->_sortColumnKey];
	}
	/**
	 * Set the table's current sort column.
	 *
	 * @param string $column The new column.
	 *
	 * @return void
	 */
	public function setSortColumn($column)
	{
		if (isset($this->_columns[$column])) {
			$this->_sortColumnKey = $column;
		}
	}



	/**
	 * Return the table's paginator.
	 *
	 * @return string
	 */
	public function getPaginator()
	{
		return $this->_paginator;
	}
	/**
	 * Set the table's paginator.
	 *
	 * @param Ztal_Table_Paginator_Abstract $paginator The new paginator.
	 *
	 * @return void
	 */
	public function setPaginator(Ztal_Table_Paginator_Abstract $paginator)
	{
		$this->_paginator = $paginator;
	}


	/**
	 * Get the data source currently set to supply data for the table.
	 *
	 * @return mixed
	 */
	public function getDataSource()
	{
		return $this->_dataSource;
	}
	
	/**
	 * Set the data source used to supply data for the table.
	 *
	 * Note that the data source is NOT cloned and MAY be modified by the table.
	 *
	 * @param mixed $data     The new data source.
	 * @param bool  $prepared Whether the data source has already been sliced
	 *                         by the paginator.
	 *
	 * @return void
	 */
	public function setDataSource($data, $prepared = false)
	{
		if (is_object($data)) {
			if (!($data instanceof ArrayAccess)) {
				throw new InvalidArgumentException(
					'Data object must support ArrayAccess interface');
			}
			if (!($data instanceof Countable)) {
				throw new InvalidArgumentException(
					'Data object must support Countable interface');
			}
		} elseif (!is_array($data)) {
			throw new InvalidArgumentException('Invalid data type');
		}
			
		
		$this->_dataSource = $data;
		
		if (!$prepared) {
			if (isset($this->_columns[$this->_sortColumnKey])) {
				$column = $this->_columns[$this->_sortColumnKey];
				if ($column->isSortable()) {
					$column->sort($this->_dataSource);
				}
			}
		
			$paginator = $this->getPaginator();
			if ($paginator != null) {
				$paginator->paginate($this->_dataSource);
			}
		}
	}


	/**
	 * An overridable method to setup columns after the constructor.
	 *
	 * @return void
	 */
	protected function _init()
	{
	}

}