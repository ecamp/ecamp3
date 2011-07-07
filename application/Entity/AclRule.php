<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann, Urban Suppiger
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
 * @Entity
 * @Table(name="acl_rules")
 */
class AclRule extends BaseEntity 
{
	const ALLOW = "allow";
	const DENY  = "deny";
	
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * @Column(type="string", nullable=false)
	 * @var String
	 */
	private $type;
	
	/**
	 * @Column(type="string", nullable=false)
	 * @var String
	 */
	private $resource;
	
	/**
	 * @Column(type="string", nullable=true)
	 * @var String
	 */
	private $privileg;
	
	/**
	 * @ManyToOne(targetEntity="AclRole")
	 * @JoinColumn(nullable=false)
	 * @var \Entity\AclRole
	 */
	private $aclRole;

	
	public function __construct(\Entity\AclRole $role)
	{
		$this->aclRole = $role;
	}
	

	public function getId()		{	return $this->id;	}
	
	public function isAllowing()			{	return (self::ALLOW == $this->type);	}
	public function isDenying()				{	return (self::DENY  == $this->type);	}
	
	public function getType()				{	return $this->type;	}
	public function setType($type)			{	$this->type = $type;	return $this;	}
	
	public function getResource()			{	return $this->resource;	}
	public function setResource($resource)	{	$this->resource = $resource;	return $this;	}
	
	public function getPrivileg()			{	return $this->privileg;	}
	public function setPrivileg($privileg)	{	$this->privileg = $privileg;	return $this;	}
	
	/** @return \Entity\AclRole */
	public function getAclRole()	{	return $this->aclRole;	}
	
	
	
	
	
	
}
