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
use EcampLib\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\UserRepository")
 * @ORM\Table(name="users")
 *
 */
class User
    extends AbstractCampOwner
    implements RoleInterface, ResourceInterface
{
    const STATE_NONREGISTERED 	= "NonRegistered";
    const STATE_REGISTERED 		= "Registered";
    const STATE_ACTIVATED  		= "Activated";
    const STATE_DELETED			= "Deleted";

    const ROLE_GUEST			= Acl::ROLE_GUEST;
    const ROLE_USER				= "User";
    const ROLE_ADMIN			= "Admin";

    const GENDER_MALE 			= 'male';
    const GENDER_FEMALE			= 'female';

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
        parent::__construct();

        $this->collaborations  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relationshipFrom = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relationshipTo   = new \Doctrine\Common\Collections\ArrayCollection();

        $this->state = self::STATE_NONREGISTERED;
        $this->role  = self::ROLE_USER;
    }

    /**
     * Unique username, lower alphanumeric symbols and underscores only
     * @ORM\Column(type="string", length=32, nullable=true, unique=true )
     */
    private $username;

    /**
     * e-mail address, unique
     * @ORM\Column(type="string", length=64, nullable=true, unique=true )
     */
    private $email;

    /**
     * untrusted e-mail address
     * @ORM\Column(type="string", length=64, nullable=true )
     */
    private $untrustedEmail;

    /**
     * ActivationCode to verify eMail address
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $emailVerificationCode;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $scoutname;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $firstname;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $surname;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $street;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $zipcode;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $city;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $homeNr;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $mobilNr;

    /** @ORM\Column(type="date", nullable=true) */
    private $birthday;

    /** @ORM\Column(type="string", length=32, nullable=true ) */
    private $ahv;

    /** @ORM\Column(type="string", length=8, nullable=true) */
    private $gender;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $jsPersNr;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $jsEdu;

    /** @ORM\Column(type="string", length=16, nullable=true ) */
    private $pbsEdu;

    /** @ORM\Column(type="string", nullable=false ) */
    private $state;

    /** @ORM\Column(type="string", nullable=false ) */
    private $role;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @var Login
     * @ORM\OneToOne(targetEntity="Login", mappedBy="user")
     */
    private $login;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="user")
     */
    protected $collaborations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    protected $memberships;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="UserRelationship", mappedBy="from", cascade={"all"}, orphanRemoval=true )
     */
    protected $relationshipFrom;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="UserRelationship", mappedBy="to", cascade={"all"}, orphanRemoval= true)
     */
    protected $relationshipTo;

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
    public function setEmail($email)
    {
        $this->email = $email;

        if ($this->state == self::STATE_NONREGISTERED) {
            $this->setUntrustedEmail($email);
        }

        return $this;
    }

    public function getUntrustedEmail()
    {
        return $this->untrustedEmail;
    }
    public function setUntrustedEmail($untrustedEmail)
    {
        $this->untrustedEmail = $untrustedEmail; return $this;
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
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }
    public function setBirthday(\DateTime $birthday = null)
    {
        $this->birthday = $birthday ? clone $birthday : null;
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
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    public function setGender( $gender )
    {
        $this->gender = $gender;
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
        $this->jsEdu = $jsEdu;
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
        $this->pbsEdu = $pbsEdu;
    }

    public function getRole()
    {
        return $this->role;
    }
    public function setRole($role)
    {
        $this->role = $role;
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
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    public function delImage()
    {
        $this->image = null;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (!empty($this->scoutname)) {
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
        if (!empty($this->scoutname)) {
            $name .= $this->scoutname.", ";
        }

        return $name.$this->firstname . " " . $this->surname;
    }

    /**
     * @return boolean
     */
    public function isMale()
    {
        return ( $this->gender === self::GENDER_MALE );
    }

    /**
     * @return boolean
     */
    public function isFemale()
    {
        return ( $this->gender === self::GENDER_FEMALE );
    }

    public function getRoleId()
    {
        return $this->getRole();
    }

    public function getResourceId()
    {
        return 'EcampCore\Entity\User';
    }

    /****************************************************************
     * User Activation:
     *
     * - createNewActivationCode
     * - checkActivationCode
     * - activateUser
     * - resetActivationCode
     ****************************************************************/

    public function createNewActivationCode()
    {
        $guid = hash('sha256', uniqid(md5(mt_rand()), true));
        $this->emailVerificationCode = md5($guid);

        return $guid;
    }

    /**
     * @deprecated
     */
    public function getEmailVerificationCode()
    {
        return $this->emailVerificationCode;
    }

    public function checkEmailVerificationCode($verificationCode)
    {
        $code = md5($verificationCode);

        return $code == $this->emailVerificationCode;
    }

    public function verifyEmail($verificationCode)
    {
        if ($this->checkEmailVerificationCode($verificationCode)) {
            $this->setEmail($this->getUntrustedEmail());
            $this->setUntrustedEmail(null);
            $this->emailVerificationCode = null;

            return true;
        }

        return false;
    }

    public function activateUser($verificationCode)
    {
        if ($this->checkEmailVerificationCode($verificationCode)) {
            $this->state = self::STATE_ACTIVATED;
            $this->setUntrustedEmail(null);
            $this->emailVerificationCode = null;

            return true;
        }

        return false;
    }

    public function resetActivationCode()
    {
        $this->emailVerificationCode = null;
    }

    /**
     * @return UserRelationshipHelper
     */
    public function userRelationship()
    {
        return new UserRelationshipHelper($this->relationshipTo, $this->relationshipFrom);
    }

    /**
     * @return UserMembershipHelper
     */
    public function groupMembership()
    {
        return new UserMembershipHelper($this->memberships);
    }

    /**
     * @return UserCollaborationHelper
     */
    public function campCollaboration()
    {
        return new UserCollaborationHelper($this->collaborations);
    }

}
