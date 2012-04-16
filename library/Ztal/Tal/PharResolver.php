<?php
/**
 * Source Resolver for finding files in a Phar archive.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Alex Mace <amace@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Source Resolver for finding files in a Phar archive.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Alex Mace <amace@names.co.uk>
 */
class Ztal_Tal_PharResolver implements PHPTAL_SourceResolver
{

	/**
	 * Repositories that source can be found it.
	 * 
	 * @var array 
	 */
	private $_repositories;

	/**
	 * Class Constructor.
	 * 
	 * @param array $repositories Array of repositories source can be found in.
	 */
	public function __construct($repositories) 
	{
		$this->_repositories = $repositories;
	}

	/**
	 * Implementation of path resolving when Phars are in the list of repos.
	 * 
	 * @param string $path Path being looked for.
	 * 
	 * @return null|PHPTAL_StringSource 
	 */
	public function resolve($path) 
	{
		foreach ($this->_repositories as $repository) {
			$file = $repository . DIRECTORY_SEPARATOR . $path;
			if (strpos($file, 'phar://') === 0 && file_exists($file)) {
				return new PHPTAL_StringSource(file_get_contents($file), $file);
			}
		}

		return null;
	}
	
}