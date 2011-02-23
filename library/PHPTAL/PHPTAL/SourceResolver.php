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
 * @version  SVN: $Id: SourceResolver.php 576 2009-04-24 10:11:33Z kornel $
 * @link     http://phptal.org/
 */

/**
 * @package PHPTAL
 */
interface PHPTAL_SourceResolver
{
    /**
     * Returns PHPTAL_Source or null.
     */
    public function resolve($path);
}
