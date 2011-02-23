<?php
/**
 * PHPTAL templating engine
 *
 * PHP Version 5
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version  SVN: $Id: Trigger.php 805 2009-12-06 01:24:28Z kornel $
 * @link     http://phptal.org/
 */


/**
 * Interface for Triggers (phptal:id)
 *
 * @package PHPTAL
 */
interface PHPTAL_Trigger
{
    const SKIPTAG = 1;
    const PROCEED = 2;

    public function start($id, $tpl);

    public function end($id, $tpl);
}
