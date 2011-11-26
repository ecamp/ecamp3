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
 * Connection between User and camp
 * User can send requests to Managers
 * Managers can send invitations to Users
 * @Entity
 * @Table(name="user_camps", uniqueConstraints={@UniqueConstraint(name="user_camp_unique",columns={"user_id","camp_id"})})
 */
class UserCamp extends BaseEntity
{
	const ROLE_NONE    = 0;
	const ROLE_GUEST   = 10;
	const ROLE_NORMAL  = 50;
	const ROLE_MANAGER = 90;
	const ROLE_OWNER   = 100;

	public function __construct(User $user = null, Camp $camp = null)
	{
		$this->role = self::ROLE_NONE;
		$this->user = $user;
		$this->camp = $camp;

		$this->invitationAccepted = false;
		$this->requestedRole = null;
		$this->requestAcceptedBy = null;
	}


	/**
	 * @return \CoreApi\Entity\UserCamp
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\UserCamp($this);
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
	private $user;

	/**
	 * @ManyToOne(targetEntity="Camp")
	 * @JoinColumn(nullable=false)
	 */
	private $camp;

	/**
	 * The role, a user currently have in this camp
	 * @Column(type="integer")
	 */
	private $role;

	/**
	 * The role, a user requested or was invited to have in this camp
	 * null = no open request
	 * @Column(type="integer", nullable=true)
	 */
	private $requestedRole;

	/**
	 * Id of the user who accepted the request
	 * null = request has not been accepted yet
	 * automatically set if an invitation is sent by a manager
	 * @var User
	 * @ManyToOne(targetEntity="User")
	 */
	private $requestAcceptedBy;

	/**
	 * True if the user has accepted the invitation
	 * automatically set to true, if request is made by user
	 * @Column(type="boolean")
	 */
	private $invitationAccepted;

	/**
	 * @Public:Method()
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setCamp(Camp $camp)
	{
		$this->camp = $camp;
	}
	
	/**
	 * @Public:MethodEntity()
	 * @return Camp
	 */
	public function getCamp()          
	{
		return $this->camp;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * @Public:MethodEntity()
	 * @return User
	 */
	public function getUser()          
	{
		return $this->user;
	}

	/**
	 * @Public:Method()
	 * @return int
	 */
	public function getRole()          
	{
		return $this->role;
	}

	/**
	 * @Public:Method()
	 * @return int
	 */
	public function getRequestedRole() 
	{
		return $this->requestedRole;
	}
	public function setRequestedRole($role) 
	{
		$this->requestedRole = $role; return $this;
	}

	/**
	 * True if the role is member or manager
	 * @Public:Method()
	 * @return boolean 
	 */
	public function isMember()
	{
		return $this->role != self::ROLE_NONE;
	}

	/** 
	 * True if the request/invitation is still open
	 * @Public:Method()
	 * @return boolean 
	 */
	public function isOpen()
	{
		return !isset($this->requestedRole);
	}

	/**
	 * True if the user sent this request to a manager and the request is still open
	 * @Public:Method()
	 * @return boolean 
	 */
	public function isOpenRequest()
	{
		return $this->isOpen() && !isset($this->requestAcceptedBy);
	}

	/**
	 * True if a manager has sent this invitation to a user and the invitation is still open
	 * @Public:Method()
	 * @return boolean 
	  */
	public function isOpenInvitation()
	{
		return $this->isOpen() && !$this->invitationAccepted;
	}

	/** user accepts invitation */
	public function acceptInvitation()
	{
		$this->invitationAccepted = true;

		if( $this->requestAcceptedBy != null )
		{
			$this->accept();
		}

		return $this;
	}

	/** manager accepts the request */
	public function acceptRequest($user)
	{
		$this->requestAcceptedBy = $user;

		if( $this->invitationAccepted )
		{
			$this->accept();
		}

		return $this;
	}

	private function accept()
	{
		$this->role = $this->requestedRole;
		$this->requestedRole = null;
		return $this;
	}


	public static function RoleFilter($role)
	{
		return
		function (UserCamp $usercamp) use ($role)
		{
			return $usercamp->getRole() == $role;
		};
	}
}
