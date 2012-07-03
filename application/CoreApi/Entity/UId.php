<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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

namespace CoreApi\Entity;

/**
 * @Entity
 * @Table(name="uid")
 */
class UId
{
	/**
	 * @var int
	 * @Id @Column(type="string")
	 */
	private $id;
	
	
	/**
	 * @var string
	 * @Column(type="string")
	 */
	private $class;
	
	
	public function __construct($class){
		$this->id = base_convert(crc32(uniqid()), 10, 16);
		$this->class = $class;
	}
	
	
	public function getId(){
		return $this->id;
	}
	
	
	public function getClass(){
		return $this->class;
	}
	
}