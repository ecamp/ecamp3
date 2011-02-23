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
 * @version  SVN: $Id: Text.php 610 2009-05-24 00:32:13Z kornel $
 * @link     http://phptal.org/
 */


/**
 * Document text data representation.
 *
 * @package PHPTAL
 * @subpackage Dom
 */
class PHPTAL_Dom_Text extends PHPTAL_Dom_Node
{
    public function generateCode(PHPTAL_Php_CodeWriter $codewriter)
    {
        if ($this->getValueEscaped() !== '') {
            $codewriter->pushHTML($codewriter->interpolateHTML($this->getValueEscaped()));
        }
    }
}
