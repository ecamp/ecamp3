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

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampLib\Entity\BaseEntity;
use EcampCore\Acl\BelongsToParentResource;

/**
 * Relationship between users (friends, etc.)
 * A Friendship needs one row in each direction. A single row only consitutes a invitation.
 * @ORM\Entity(repositoryClass="EcampCore\Repository\UserRelationshipRepository")
 * @ORM\Table(name="user_relationships", uniqueConstraints={@ORM\UniqueConstraint(name="from_to_unique",columns={"from_id","to_id"})})
 */
class UserRelationship 
	extends BaseEntity
	implements BelongsToParentResource
{
	const TYPE_FRIEND  = 1;
	// const TYPE_BLOCK   = 2;

	public function __construct($from = null, $to = null, $type = self::TYPE_FRIEND)
	{
		parent::__construct();
		
		$this->type  = $type;
		$this->from  = $from;
		$this->to  = $to;
	}



	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $from;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $to;

	/**
	 * Type of the relationship
	 * @ORM\Column(type="integer")
	 */
	private $type;

	/**
	 * @ORM\OneToOne(targetEntity="UserRelationship")
	 * @ORM\JoinColumn(name="counterpart", referencedColumnName="id")
	 * @var UserRelationship
	 */
	private $counterpart;

	
	/**
	 * @return User
	 */
	public function getFrom() 
	{
		return $this->from;
	}
	
	public function getParentResource(){
		return $this->from;
	}
	
	
	/**
	 * @return User
	 */
	public function getTo()   
	{
		return $this->to;
	}
	
	
	/**
	 * @return int
	 */
	public function getType() 
	{
		return $this->type;
	}
	
	
	/**
	 * @return UserRelationship
	 */
	public function getCounterpart()
	{
		return $this->counterpart;
	}

	
	public static function Link(UserRelationship $ur1, UserRelationship $ur2)
	{
		$ur1->counterpart = $ur2;
		$ur2->counterpart = $ur1;
	}
	
}
