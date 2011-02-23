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

class BootStrapTest
	extends PHPUnit_Framework_TestCase
{

	private $application;

	function setUp()
	{
		$this->application = new Zend_Application(
			APPLICATION_ENV,
			APPLICATION_PATH . '/configs/application.ini'
		);
	}


	function testDoctrineBootstrap()
	{
		$this->application->bootstrap('doctrine');

	//	$this->assertNotNull(Zend_Registry::get('entitymanager'));
	}

	function testZTalBootstrap()
	{
		$this->application->bootstrap('ztal');
	}

	function testRoutesBootstrap()
	{
		$this->application->bootstrap('routes');
	}


}
