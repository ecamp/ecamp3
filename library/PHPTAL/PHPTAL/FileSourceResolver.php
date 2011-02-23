<?php
/**
 * PHPTAL templating engine
 *
 * PHP Version 5
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @author   Kornel Lesiński <kornel@aardvarkmedia.co.uk>
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version  SVN: $Id: FileSourceResolver.php 868 2010-05-25 22:27:39Z kornel $
 * @link     http://phptal.org/
 */


/**
 * Finds template on disk by looking through repositories first
 *
 * @package PHPTAL
 */
class PHPTAL_FileSourceResolver implements PHPTAL_SourceResolver
{
    public function __construct($repositories)
    {
        $this->_repositories = $repositories;
    }

    public function resolve($path)
    {
        foreach ($this->_repositories as $repository) {
            $file = $repository . DIRECTORY_SEPARATOR . $path;
            if (file_exists($file)) {
                return new PHPTAL_FileSource($file);
            }
        }

        if (file_exists($path)) {
            return new PHPTAL_FileSource($path);
        }

        return null;
    }

    private $_repositories;
}
