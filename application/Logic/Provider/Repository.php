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

namespace Logic\Provider;

class Repository
	implements \Inject\Syntax\IProvider
{

	/**
	 * @var string
	 */
	private $entityName;


	public function __construct($entityName)
	{
		$this->entityName = $entityName;
	}

	public function Create()
	{
		$em = \Zend_Registry::get("doctrine")->getEntityManager();

		return $em->getRepository($this->entityName);
	}
	
	public static function Get($entityName)
	{
		$provider = new self($entityName);
		return $provider->Create();
	}
}
