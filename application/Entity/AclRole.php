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
 * @Table(name="acl_roles")
 */
class AclRole 
	extends BaseEntity 
	implements \Zend_Acl_Role_Interface
{

	
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
	private $name;
	
	/**
	 * @Column(type="string")
	 * @var String
	 */
	private $description;
	
	/**
	 * @ManyToOne(targetEntity="Camp")
	 * @JoinColumn(nullable=false)
	 * @var \Entity\Camp
	 */
	private $camp;
	
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="Entity\AclRule", mappedBy="aclRole", cascade={"all"}, orphanRemoval=true)
	 */
	private $aclRules;
	

	
	public function __construct(\Entity\Camp $camp)
	{
		$this->aclRules = new \Doctrine\Common\Collections\ArrayCollection();
		$this->camp = $camp;
	}
	

	public function getId()		{	return $this->id;	}

	public function getRoleId()	{	return "ROLE_" . $this->id;	}
	
	public function getName()			{	return $this->name;	}
	public function setName($name)		{	$this->name = $name; 	return $this;	}
	
	public function getDescription()		{	return $this->descriptio;	}
	public function setDescription($desc)	{	$this->description = $desc;	return $this;	}
	
	/** @return \Entity\Camp */
	public function getCamp()			{	return $this->camp;	}
	
	/** @return \Doctrine\Common\Collections\ArrayCollection */
	public function getAclRules()		{	return $this->aclRules;	}
	
}
