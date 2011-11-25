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
 * Relationship between users (friends, etc.)
 * A Friendship needs one row in each direction. A single row only consitutes a invitation.
 * @Entity
 * @Table(name="user_relationships", uniqueConstraints={@UniqueConstraint(name="from_to_unique",columns={"from_id","to_id"})})
 */
class UserRelationship extends BaseEntity
{
	const TYPE_FRIEND  = 1;
	// const TYPE_BLOCK   = 2;

	public function __construct($from = null, $to = null, $type = self::TYPE_FRIEND)
	{
		$this->type  = $type;
		$this->from  = $from;
		$this->to  = $to;
	}


	/**
	 * @return \CoreApi\Entity\UserRelationship
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\UserRelationship($this);
	}


	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(nullable=false)
	 */
	private $from;

	/**
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(nullable=false)
	 */
	private $to;

	/**
	 * Type of the relationship
	 * @Column(type="integer")
	 */
	private $type;

	
	/**
	 * @Public:Method()
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	
	/**
	 * @Public:MethodEntity()
	 * @return User
	 */
	public function getFrom() 
	{
		return $this->from;
	}
	
	
	/**
	* @Public:MethodEntity()
	* @return User
	*/
	public function getTo()   
	{
		return $this->to;
	}
	
	
	/**
	* @Public:Method()
	* @return int
	*/
	public function getType() 
	{
		return $this->type;
	}

}
