<?php
/**
 * Abstract class to handle pagination in html tables.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Abstract class to handle pagination in html tables.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Table_Paginator_Abstract
{
	/**
	 * The total number of rows to paginate.
	 *
	 * @var int
	 */
	protected $_totalRowCount;
	
	/**
	 * The number of rows to display per page.
	 *
	 * @var int
	 */
	protected $_rowsPerPage;
	
	/**
	 * The page to be displayed.
	 *
	 * @var int
	 */
	protected $_currentPage;
	

	/**
	 * Constructor.
	 *
	 * @param array $options Optional array of config options.
	 */
	public function __construct(array $options = array())
	{
		$this->_totalRowCount = 0;
		$this->_rowsPerPage = 1;
		$this->_currentPage = 0;
		
		if (isset($options['rowsPerPage'])) {
			$this->setRowsPerPage($options['rowsPerPage']);
		}
		
		if (isset($options['currentPage'])) {
			$this->setCurrentPage($options['currentPage']);
		}
	}
	
	
	/**
	 * Apply configuration parameters as passed from a rendered html table.
	 *
	 * @param array  $parameters The configuration parameters.
	 * @param string $prefix     Optional prefix to use for parameter keys.
	 *
	 * @return void
	 */
	public function initWithParameters(array $parameters, $prefix = '')
	{
		if (isset($parameters[$prefix . 'page'])) {
			$this->_currentPage = $parameters[$prefix . 'page'];
		}
	}
	
	
	/**
	 * Apply pagination to a data source.
	 *
	 * The source is count()'ed and the totalRowCount of the paginator
	 * updated before the source is then sliced to represent a single page.
	 *
	 * Note: This WILL modify the supplied source.
	 *
	 * @param mixed &$source The source to paginate.
	 *
	 * @return void
	 */
	public function paginate(&$source)
	{
		$this->_totalRowCount = count($source);
		$startingRow = $this->_rowsPerPage * $this->_currentPage;
		if ($startingRow > $this->_totalRowCount) {
			$startingRow = 0;
			$this->_currentPage = 0;
		}

		$this->_sliceDataSource($source, $startingRow, $this->_rowsPerPage);
	}
	


	/**
	 * Return true if there are multiple pages.
	 *
	 * @return bool
	 */
	public function isMultipage()
	{
		return ceil($this->_totalRowCount / $this->_rowsPerPage) > 1;
	}


	/**
	 * Return details about all the pages.
	 *
	 * @return array
	 */
	public function pages()
	{
		$pageCount = ceil($this->_totalRowCount / $this->_rowsPerPage);
		$results = array();
		for ($i = 0; $i < $pageCount; $i++) {
			$results[] = array(
				'index' => $i,
				'label' => $i + 1,
				'currentPage' => $i == $this->_currentPage,
			);
		}
		return $results;
	}


	/**
	 * Return the index of the page before the current page.
	 *
	 * @return int|null
	 */
	public function previousPage()
	{
		return $this->_currentPage - 1;
	}
	
	
	/**
	 * Return the index of the page after the current one, or null.
	 *
	 * @return int|null
	 */
	public function nextPage()
	{
		$lastPage = (ceil($this->_totalRowCount / $this->_rowsPerPage) - 1);
		if ($this->_currentPage == $lastPage) {
			return -1;
		}
		return $this->_currentPage + 1;
	}

	
	/**
	 * Return the total number of rows in the data set.
	 *
	 * @return int
	 */
	public function getTotalRowCount()
	{
		return $this->_totalRowCount;
	}
	
	/**
	 * Set the total number of rows in the data set.
	 *
	 * @param int $count The total row count.
	 *
	 * @return void
	 */
	public function setTotalRowCount($count)
	{
		if ($count < 0) {
			$count = 0;
		}
		$this->_totalRowCount = $count;
	}
	
	
	/**
	 * Return the current page number.
	 *
	 * @return int
	 */
	public function getCurrentPage()
	{
		return $this->_currentPage;
	}
	
	/**
	 * Set the current page number.
	 *
	 * @param int $page The page number.
	 *
	 * @return void
	 */
	public function setCurrentPage($page)
	{
		if ($page >= 0 && $page * $this->_rowsPerPage < $this->_totalRowCount) {
			$this->_currentPage = $page;
		}
	}
	
	
	/**
	 * Return how many rows are shown on a page.
	 *
	 * @return int
	 */
	public function getRowsPerPage()
	{
		return $this->_rowsPerPage;
	}
	
	/**
	 * Set how many rows are shown on a page.
	 *
	 * @param int $rowsPerPage The number or rows to show per page.
	 *
	 * @return void
	 */
	public function setRowsPerPage($rowsPerPage)
	{
		if ($rowsPerPage < 1) {
			$rowsPerPage = 1;
		}
		$this->_rowsPerPage = $rowsPerPage;
	}
	
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
		throw new Exception('Invalid call to method in Abstract class');
	}

}