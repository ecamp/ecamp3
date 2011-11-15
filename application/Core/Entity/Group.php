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

namespace Core\Entity;

/**
 * @Entity
 * @Table(name="groups", indexes={@index(name="group_name_idx", columns={"name"})}, uniqueConstraints={@UniqueConstraint(name="group_parent_name_unique",columns={"parent_id","name"})})
 */
class Group extends BaseEntity
{
	public function __construct()
    {
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
	    $this->camps = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Short identifier, unique inside parent group
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $name;
	
	/**
	 * @ManyToOne(targetEntity="Group", inversedBy="children")
	 */
	private $parent;
	
	/**
     * @OneToMany(targetEntity="Group", mappedBy="parent")
	 * @OrderBy({"name" = "ASC"})
     */
	private $children;
	
	/** 
	 * @Column(type="string", length=64, nullable=false ) 
	 */
	private $description;
	
	/**
	 * @OneToMany(targetEntity="UserGroup", mappedBy="group", cascade={"all"}, orphanRemoval=true )
	 */
	private $userGroups;
	
	/**
	 * @var Camp
	 * @OneToMany(targetEntity="Camp", mappedBy="group")
	 */
	private $camps;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $imageMime;
	
	/** @Column(type="object", nullable=true ) */
	private $imageData;
	
	
	public function getId(){	return $this->id;	}
	
	public function getName()   { return $this->name; }
	public function setName( $name ){ $this->name = $name; return $this; }
	
	public function getDescription()   { return $this->description; }
	public function setDescription( $description ){ $this->description = $description; return $this; }

	public function getParent()   { return $this->parent; }
	public function setParent( $parent ){ $this->parent = $parent; return $this; }
	
	public function getChildren() { return $this->children; }
	public function hasChildren() { return ! $this->children->isEmpty(); }
	
	public function getUserGroups() { return $this->userGroups; }

	public function getCamps(){ return $this->camps; }

	public function getMembers()
    {
	    $members = new \Doctrine\Common\Collections\ArrayCollection();

	    foreach($this->userGroups as $userGroup) {
			if($userGroup->isMember()) {
				$members->add($userGroup->getUser());
			}
		}

		return $members;
	}
	
	public function getPathAsArray()
	{
		$path = array();
		$group = $this;
		
		while( $group->getParent() != null )
		{
			$group = $group->getParent();
			$path[] = $group;
		}
		
		return array_reverse($path);
	}
	
	public function getImageData(){ return base64_decode($this->imageData); }
	public function setImageData($data){ $this->imageData = base64_encode($data); return $this; }
	
	public function getImageMime(){ return $this->imageMime; }
	public function setImageMime($mime){ $this->imageMime = $mime; return $this; }
	
	public function delImage(){
		$this->imageMime = null;
		$this->imageData = null;
		return $this;
	}
	
	public function isManager($user) {
		$closure =  function($key, $element) use ($user){ 
			return  $element->getRole() == UserGroup::ROLE_MANAGER && $element->getUser() == $user;
		};
		
		return $this->getUserGroups()->exists( $closure ); 
	}
	
	public function acceptRequest($request, $manager) {
		if( $this->isManager($manager) )
			$request->acceptRequest($manager);
		
		return $this;
	}
	
	public function refuseRequest($request, $manager) {
		if( $this->isManager($manager) ) {
			$request->getUser()->getUserGroups()->removeElement($request);
			$this->userGroups->removeElement($request);
		}
		
		return $this;
	}
}