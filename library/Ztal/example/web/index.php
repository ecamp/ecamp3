<?php
/**
 * Global Despatch starting point for all Zend requests.
 *
 * @category  Namesco
 * @package   Global
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH',
	realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV',
	(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.xml');
$application->setBootstrap(APPLICATION_PATH . '/Bootstrap.php', 'Bootstrap');
$application->bootstrap()->run();
