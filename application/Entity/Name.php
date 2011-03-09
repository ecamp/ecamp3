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

namespace Entity;

/**
 * This entity defines the shared namespace of usernames and group names
 * @Entity
 * @Table(name="names", uniqueConstraints={@UniqueConstraint(name="name_idx", columns={"name"})})
 */
class Name extends BaseEntity
{
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/** @Column(type="string", length=32, nullable=false ) */
	private $name;
	
	public function getId(){ return $this->id; }
	
	public function getName()   { return $this->name; }
	public function setName( $name ){ $this->name = $name; return $this; }

}