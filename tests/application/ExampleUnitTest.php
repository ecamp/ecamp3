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

class ObjectTest
	extends PHPUnit_Framework_TestCase
{

	private $em;
	private $demoUser;


	/**
	 * Die Methode setUp wird vor jeder Test-Methode
	 * aufgerufen. Sie wird benutzt um allgemein gültige
	 * Vorbereitungen für die Tests zu zentralisieren.
	 *
	 * @return void
	 */
	function setUp()
	{
		$this->em = $GLOBALS['bootstrap']->em;



		$demoUser = $this->getMock('User', array('getScoutname'));

		$demoUser
			->expects($this->any())
			->method('getScoutname')
			->will($this->returnValue('SomeScoutName'));

		$this->demoUser = $demoUser;
	}


	/**
	 * Überprüft, ob das new-Statement funktioniert.
	 *
	 * @return void
	 */
	function testCreateObject()
	{
		$object = new stdClass();

		$this->assertNotNull($object);
	}


	/**
	 * Ein Beispiel eines MockObjektes.
	 *
	 * @return void
	 */
	function testWithMockObject()
	{
		$this->assertEquals('SomeScoutName', $this->demoUser->getScoutname());
	}

}
