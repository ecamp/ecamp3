<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));

// Define application environment
define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path()
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once '../tests/ServiceTestCase.php';
// require_once '../tests/EcampTestCase.php';
// require_once '../tests/EcampTestCaseWithDb.php';
// require_once '../tests/EcampControllerTestCase.php';

//require_once('./application/EcampTestCase.php');
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);


$application->bootstrap();




$doctrineContainer = Zend_Registry::get('doctrine');
$em = $doctrineContainer->getEntityManager();

$sm = new SchemaManager($em);
Zend_Registry::set('SchemaManager', $sm);

$sm->dropAllTables();
$sm->createSchema();
$sm->runSqlFile(APPLICATION_PATH . '/../data/sql/eCamp3dev.sql');


unset($doctrineContainer);
unset($em);
unset($sm);


clearstatcache();





