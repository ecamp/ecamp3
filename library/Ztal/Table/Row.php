<?php
/**
 * Represent a row in an html table.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Represent a row in an html table.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Row implements Countable, Iterator, ArrayAccess
{
	/**
	* The array of columns as provided in the constructor.
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
	 * The data source that provides data for the current row.
	 *
	 * @var mixed
	 */
	protected $_rowDataSource;
	

	/**
	 * Track the current column used in the iterator.
	 *
	 * @var int
	 */
	protected $_currentColumnIndex;
	
	
	/**
	 * Constructor.
	 *
	 * @param array &$columnArray    A ref to the Table instance's column array.
	 * @param array &$columnKeyIndex A ref to the Table instance's key index.
	 */
	public function __construct(&$columnArray, &$columnKeyIndex)
	{
		// We want to hold a reference to the column array in the
		// parent Table object.
		$this->_columns = &$columnArray;
		$this->_columnKeyIndex = &$columnKeyIndex;
		
		$this->_currentColumnIndex = 0;
	}
	




	// ** ArrayAccess **
	//********************************************
	
	/**
	 * ArrayAccess Interface. Checks if an offset is set.
	 *
	 * @param mixed $offset The offset (traditionally called the key) to check
	 *						for existence.
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->_columns[$offset]);
	}
	

	/**
	 * ArrayAccess Interface. Returns the value for an offset.
	 *
	 * @param mixed $offset The offset (traditionally called the key) to return.
	 *
	 * @return mixed Returns the value associated with the offset.
	 * @throws OutOfBoundsException If the specified offset doesn't exist.
	 */
	public function offsetGet($offset)
	{
		if (!isset($this->_columns[$offset])) {
			throw new OutOfBoundsException('Invalid offset');
		}
		return $this->_columns[$offset]->getColumnDataForSource($this->_rowDataSource);
	}
	
	
	/**
	 * ArrayAccess Interface. Sets the value for an offset.
	 *
	 * @param mixed $offset The offset (traditionally called the key) to set a
	 *					          value for.
	 * @param mixed $item   The value to set.
	 *
	 * @return void
	 * @throws Exception We don't support setting of array values.
	 */
	public function offsetSet($offset, $item)
	{
		throw new Exception('Setting of row columns is not permitted');
	}
	

	/**
	 * ArrayAccess Interface. Unsets an offset.
	 *
	 * @param mixed $offset The offset (traditionally called the key) to unset.
	 *
	 * @return void
	 * @throws Exception We don't support unsetting of array values.
	 */
	public function offsetUnset($offset)
	{
		throw new Exception('Unsetting of row columns is not permitted');
	}


	// ** Countable **
	//********************************************
	
	/**
	 * Countable Interface. How many columns the table has.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->_columns);
	}


	// ** Iterator **
	//********************************************

	
	/**
	 * Iterator Interface. Returns the current value.
	 *
	 * @return mixed The value for the current offset.
	 */
	public function current()
	{
		return $this->_columns[$this->_columnKeyIndex[$this->_currentColumnIndex]]
						->getColumnDataForSource($this->_rowDataSource);
	}
	
	
	/**
	 * Iterator Interface. Returns the current key.
	 *
	 * @return mixed The current offset.
	 */
	public function key()
	{
		if (!isset($this->_columnKeyIndex[$this->_currentColumnIndex])) {
			throw new OutOfBoundsException('Invalid offset ' . $this->_currentColumnIndex);
		}
		
		return $this->_columnKeyIndex[$this->_currentColumnIndex];
		
	}

	/**
	 * Iterator Interface. Increments the iterator and returns the new value.
	 *
	 * @return void
	 */	
	public function next()
	{
		$this->_currentColumnIndex++;
	}
	
	/**
	 * Iterator Interface. Resets the iterator to the start of the sequence.
	 *
	 * @return void
	 */	
	public function rewind()
	{
		$this->_currentColumnIndex = 0;
	}

	/**
	 * Iterator Interface. Checks if the current offset is valid.
	 *
	 * @return bool.
	 */	
	public function valid()
	{
		return ($this->_currentColumnIndex >= 0 && $this->_currentColumnIndex < count($this->_columns));
	}



	
	/**
	 * Set the data source providing data for the row.
	 *
	 * @param mixed $rowDataSource The data source.
	 *
	 * @return void
	 */
	public function setRowDataSource($rowDataSource)
	{
		$this->_rowDataSource = $rowDataSource;
		$this->_currentColumnIndex = 0;
	}
	
	/**
	 * Get the current data source.
	 *
	 * @return mixed
	 */
	public function getRowDataSource()
	{
		return $this->_rowDataSource;
	}
	
}