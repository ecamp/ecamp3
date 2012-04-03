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
 * @Entity(repositoryClass="\Core\Repository\UserRepository")
 * @Table(name="users")
 *
 */
class User extends BaseEntity
{
	const STATE_NONREGISTERED 	= "NonRegistered";
	const STATE_REGISTERED 		= "Registered";
	const STATE_ACTIVATED  		= "Activated";
	const STATE_DELETED			= "Deleted";

	const ROLE_USER				= "User";
	const ROLE_ADMIN			= "Admin";

	const GENDER_FEMALE			= true;
	const GENDER_MALE 			= false;

	const JSEDU_GRUPPENLEITER 	= "Gruppenleiter";
	const JSEDU_LAGERLEITER		= "Lagerleiter";
	const JSEDU_AUSBILDNER		= "Ausbildner";
	const JSEDU_EXPERTE			= "Experte";

	const PBSEDU_BASISKURS		= "Basiskurs";
	const PBSEDU_AUFBAUKURS		= "Aufbaukurs";
	const PBSEDU_PANOKURS		= "Panokurs";
	const PBSEDU_SPEKTRUM		= "Spektrum";
	const PBSEDU_TOPKURS		= "Topkurs";
	const PBSEDU_GILLWELL		= "Gillwell";

	public function __construct()
	{
		$this->mycamps  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userCamps  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
		$this->relationshipFrom = new \Doctrine\Common\Collections\ArrayCollection();
		$this->relationshipTo   = new \Doctrine\Common\Collections\ArrayCollection();

		$this->state = self::STATE_NONREGISTERED;
		$this->role  = self::ROLE_USER;
	}

	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Unique username, lower alphanumeric symbols and underscores only
	 * @Column(type="string", length=32, nullable=true, unique=true )
	 */
	private $username;

	/**
	 * e-mail address, unique
	 * @Column(type="string", length=64, nullable=true, unique=true )
	 */
	private $email;

	/**
	 * ActivationCode to verify eMail address
	 * @Column(type="string", length=64, nullable=true)
	 */
	private $activationCode;



	/** @Column(type="string", length=32, nullable=true ) */
	private $scoutname;

	/** @Column(type="string", length=32, nullable=true ) */
	private $firstname;

	/** @Column(type="string", length=32, nullable=true ) */
	private $surname;

	/** @Column(type="string", length=32, nullable=true ) */
	private $street;

	/** @Column(type="string", length=16, nullable=true ) */
	private $zipcode;

	/** @Column(type="string", length=32, nullable=true ) */
	private $city;

	/** @Column(type="string", length=16, nullable=true ) */
	private $homeNr;

	/** @Column(type="string", length=16, nullable=true ) */
	private $mobilNr;

	/** @Column(type="date", nullable=true) */
	private $birthday;

	/** @Column(type="string", length=32, nullable=true ) */
	private $ahv;

	/** @Column(type="boolean", nullable=true) */
	private $gender;

	/** @Column(type="string", length=16, nullable=true ) */
	private $jsPersNr;

	/** @Column(type="string", length=16, nullable=true ) */
	private $jsEdu;

	/** @Column(type="string", length=16, nullable=true ) */
	private $pbsEdu;

	/** @Column(type="string", nullable=false ) */
	private $state;

	/** @Column(type="string", nullable=false ) */
	private $role;

	
	/**
	 * @var CoreApi\Entity\Image
	 * @OneToOne(targetEntity="Image")
	 * @JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

	/**
	 * Camps, which I own myself
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="Camp", mappedBy="owner")
	 */
	private $mycamps;


	/**
	 * @var Entity\Login
	 * @OneToOne(targetEntity="Login", mappedBy="user")
	 */
	private $login;


	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserCamp", mappedBy="user")
	 */
	private $userCamps;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserGroup", mappedBy="user", cascade={"all"}, orphanRemoval=true)
	 */
	private $userGroups;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserRelationship", mappedBy="from", cascade={"all"}, orphanRemoval=true )
	 */
	private $relationshipFrom;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserRelationship", mappedBy="to", cascade={"all"}, orphanRemoval= true)
	 */
	private $relationshipTo;


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUsername()           
	{
		return $this->username;
	}
	public function setUsername($username) 	
	{
		$this->username = $username; return $this;
	}

	/**
	 * @return Login
	 */
	public function getLogin()	
	{
		return $this->login;
	}

	/**
	 * @return string
	 */
	public function getEmail()            
	{
		return $this->email;
	}
	public function setEmail( $email )    
	{
		$this->email = $email; return $this;
	}

	/**
	 * @return string
	 */
	public function getScoutname()            
	{
		return $this->scoutname;
	}
	public function setScoutname( $scoutname )
	{
		$this->scoutname = $scoutname; return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstname()            
	{
		return $this->firstname;
	}
	public function setFirstname( $firstname )
	{
		$this->firstname = $firstname; return $this;
	}

	/**
	 * @return string
	 */
	public function getSurname()          
	{
		return $this->surname;
	}
	public function setSurname( $surname )
	{
		$this->surname = $surname; return $this;
	}

	/**
	 * @return string
	 */
	public function getStreet()         
	{
		return $this->street;
	}
	public function setStreet( $street )
	{
		$this->street = $street; return $this;
	}

	/**
	 * @return string
	 */
	public function getZipcode()          
	{
		return $this->zipcode;
	}
	public function setZipcode( $zipcode )
	{
		$this->zipcode = $zipcode; return $this;
	}

	/**
	 * @return string
	 */
	public function getCity()       
	{
		return $this->city;
	}
	public function setCity( $city )
	{
		$this->city = $city; return $this;
	}

	/**
	 * @return string
	 */
	public function getHomeNr()         
	{
		return $this->homeNr;
	}
	public function setHomeNr( $homeNr )
	{
		$this->homeNr = $homeNr; return $this;
	}

	/**
	 * @return string
	 */
	public function getMobilNr()          
	{
		return $this->mobilNr;
	}
	public function setMobilNr( $mobilNr )
	{
		$this->mobilNr = $mobilNr; return $this;
	}

	/**
	 * @return date
	 */
	public function getBirthday()           
	{
		return $this->birthday;
	}
	public function setBirthday( $birthday )
	{
		$this->birthday = $birthday; 
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAHV()      
	{
		return $this->ahv;
	}
	public function setAHV( $ahv )
	{
		$this->ahv = $ahv; 
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getGender()         
	{
		return $this->gender;
	}
	public function setGender( $gender )
	{
		$this->gender = (BOOLEAN) $gender; 
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJsPersNr()           
	{
		return $this->jsPersNr;
	}
	public function setJsPersNr( $jsPersNr )
	{
		$this->jsPersNr = $jsPersNr; 
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJsEdu()        
	{
		return $this->jsEdu;
	}
	public function setJsEdu( $jsEdu )
	{
		$this->jsEdu = $jsEdu; return $this;
	}

	/**
	 * @return string
	 */
	public function getPbsEdu()         
	{
		return $this->pbsEdu;
	}
	public function setPbsEdu( $pbsEdu )
	{
		$this->pbsEdu = $pbsEdu; return $this;
	}

	public function getRole()		
	{
		return $this->role;
	}
	public function setRole($role)	
	{
		$this->role = $role; return $this;
	}

	public function getState()		
	{
		return $this->state;
	}
	public function setState($state)
	{
		$this->state = $state;
	}

	
	
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function getImage()
	{
		return $this->image;
	}
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;	return $this;
	}
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function delImage()
	{
		$this->image = null;	return $this;
	}


	/**
	 * @return array
	 */
	public function getUsergroups()
	{
		return $this->userGroups;
	}

	/**
	 * @return array
	 */
	public function getMyCamps()
	{
		return $this->mycamps;
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		if( !empty( $this->scoutname ) )
		{
			return $this->scoutname;
		}

		return $this->firstname . " " . $this->surname;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		$name = "";
		if( !empty( $this->scoutname ) )
		{
			$name .= $this->scoutname.", ";
		}

		return $name.$this->firstname . " " . $this->surname;
	}


	/****************************************************************
	 * User Activation:
	****************************************************************/
	public function createNewActivationCode()
	{
		$guid = hash('sha256', uniqid(md5(mt_rand()), true));
		$this->activationCode = $guid;

		return $guid;
	}
	
	
	/**
	 * @deprecated
	 */
	public function getActivationCode()
	{
		return $this->activationCode;
	}

	public function checkActivationCode($activationCode)
	{
		$code = $activationCode;

		return $code == $this->activationCode;
	}

	public function activateUser($activationCode)
	{
		if($this->checkActivationCode($activationCode))
		{
			$this->state = self::STATE_ACTIVATED;
			$this->activationCode = null;
			return true;
		}

		return false;
	}

	public function resetActivationCode()
	{
		$this->activationCode = null;
	}
	/****************************************************************/


	/**
	 * @return boolean
	 */
	public function isMale()
	{
		return ( $this->gender == self::GENDER_MALE );
	}

	/**
	 * @return boolean
	 */
	public function isFemale()
	{
		return ( $this->gender == self::GENDER_FEMALE );
	}


	public function getAcceptedUserCamps()
	{
		$closure = function(UserCamp $element)
		{
			return $element->isMember();
		};

		return $this->userCamps->filter($closure);
	}

	/* * * * * * * * * * * * * * * * * * *\
	 * Friendship methods				 *
	\* * * * * * * * * * * * * * * * * * */

	public function getRelationshipFrom()
	{
		return $this->relationshipFrom;
	}

	public function getRelationshipTo()
	{
		return $this->relationshipTo;
	}

	private function isFriendTo($user)
	{
		$closure =  function($key, $element) use ($user){
			return $element->getType() == UserRelationship::TYPE_FRIEND && $element->getTo() == $user;
		};

		return $this->getRelationshipFrom()->exists( $closure );
	}

	private function isFriendFrom($user)
	{
		$closure =  function($key, $element) use ($user){
			return $element->getType() == UserRelationship::TYPE_FRIEND  && $element->getFrom() == $user;
		};

		return $this->getRelationshipTo()->exists( $closure );
	}

	/**
	 * True if friendship request has been sent but not yet accepted
	 *
	 * @return boolean
	 */
	public function sentFriendshipRequestTo($user)
	{
		return $this->isFriendTo( $user ) && ! $this->isFriendFrom( $user );
	}

	/**
	 * True if friendship request has been received but not yet accepted
	 * @return boolean
	 */
	public function receivedFriendshipRequestFrom($user)
	{
		return ! $this->isFriendTo( $user ) && $this->isFriendFrom( $user );
	}

	/**
	 * True for established friendships (both directions)
	 *
	 * @return boolean
	 */
	public function isFriendOf($user)
	{
		return $this->isFriendTo( $user ) && $this->isFriendFrom( $user );
	}

	/** get the relation object to $user */
	public function getRelFrom($user)
	{
		$closure =  function($element) use ($user){
			return $element->getType() == UserRelationship::TYPE_FRIEND  && $element->getFrom() == $user;
		};

		$relations =  $this->getRelationshipTo()->filter( $closure );

		if( $relations->isEmpty() )
		return null;

		return $relations->first();
	}

	/** get the relation object from $user */
	public function getRelTo($user)
	{
		$closure =  function($element) use ($user){
			return $element->getType() == UserRelationship::TYPE_FRIEND  && $element->getTo() == $user;
		};
			
		$relations =  $this->getRelationshipFrom()->filter( $closure );

		if( $relations->isEmpty() )
		return null;

		return $relations->first();
	}


	/** check  whether a friendship request can be sent to to the user */
	/**
	 * @return boolean
	 */
	public function canIAdd($user)
	{
		return $user != $this && !$this->isFriendOf($user) && !$this->sentFriendshipRequestTo($user) && !$this->receivedFriendshipRequestFrom($user);
	}


	/****************************************************************
	 * Membership methods
	****************************************************************/


	public function getMemberships() 
	{
		$closure =  function($element){
			return $element->isMember();
		};

		return $this->getUserGroups()->filter( $closure );
	}

	public function sendMembershipRequestTo($group) 
	{
		$membership = $this->getMembershipWith($group);

		if( !isset($membership) ) {
			$rel = new UserGroup($this, $group);
			$rel->setRequestedRole(UserGroup::ROLE_MEMBER);
			$rel->acceptInvitation(); /* I invite myself */
				
			$this->userGroups->add($rel);
			$group->getUserGroups()->add($rel);
		}
	}

	/**
	 * returns the relationship entity UserGroup, if it exists
	 * DB ensures, that there's one element at maximum
	 */

	private function getMembershipWith($group)
	{
		$closure =  function($element) use ($group){
			/* comparing id's is not good, but experienced recursing problems when comparing objects */
			return $element->getGroup()->getId() == $group->getId();
		};

		$memberships = $this->getUserGroups()->filter( $closure );

		if( $memberships->isEmpty() )
		return null;
			
		return $memberships->first();
	}

	public function deleteMembershipWith($group) 
	{
		$membership = $this->getMembershipWith($group);

		if( isset($membership) ) {
			$membership->getGroup()->getUserGroups()->removeElement($membership);
			$this->userGroups->removeElement($membership);
		}
	}

	public function getAcceptedUserGroups()
	{
		$closure = function(UserGroup $element)
		{
			return $element->isMember();
		};

		return $this->userGroups->filter($closure);
	}

	public function canRequestMembership($group)
	{

		$membership = $this->getMembershipWith($group);

		if( isset($membership) && ( $membership->isMember() ||  $membership->isOpenRequest() ))
		return false;
			
		return true;
	}

	public function isManagerOf($group)
	{
		$membership = $this->getMembershipWith($group);

		if( isset($membership) && $membership->isManager())
		return true;
			
		return false;
	}

	public function isMemberOf($group)
	{
		$membership = $this->getMembershipWith($group);

		if( isset($membership) && $membership->isMember())
		return true;
			
		return false;
	}

	public function hasOpenRequest($group)
	{
		$membership = $this->getMembershipWith($group);

		if( isset($membership) && $membership->isOpenRequest())
		return true;
			
		return false;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getManagedGroups()
	{
		$filter = function(UserGroup $element)
		{
			return $element->isManager();
		};

		$map = function(UserGroup $element)
		{
			return $element->getGroup();
		};

		return $this->getUserGroups()->filter($filter)->map($map);
	}



}
