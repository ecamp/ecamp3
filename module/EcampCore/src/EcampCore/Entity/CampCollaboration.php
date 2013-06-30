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

/**
 * Connection between User and camp
 * User can send requests to Managers
 * Managers can send invitations to Users
 *
 * @ORM\Entity(repositoryClass="EcampCore\Repository\CampCollaborationRepository")
 * @ORM\Table(name="user_camps", uniqueConstraints={@ORM\UniqueConstraint(name="user_camp_unique",columns={"user_id","camp_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class CampCollaboration
    extends BaseEntity
{
    const ROLE_GUEST   = 'guest';
    const ROLE_MEMBER  = 'member';
    const ROLE_MANAGER = 'manager';
    const ROLE_OWNER   = 'owner';

    const STATUS_REQUESTED 		= 'requested';
    const STATUS_INVITED 		= 'invited';
    const STATUS_ESTABLISHED 	= 'established';

    public function __construct(User $user, Camp $camp, User $inviter = null, $status, $role)
    {
        parent::__construct();

        $this->user = $user;
        $this->camp = $camp;

        $this->setStatus($status);
        $this->setRole($role ?: self::ROLE_GUEST);

        if ($this->isInvitation()) {
            $this->setRequestAcceptedBy($inviter);
        } else {
            $this->requestAcceptedBy = null;
        }

        $this->user->getList('collaborations')->add($this);
        $this->camp->getList('collaborations')->add($this);
    }

    public static function createRequest(User $user, Camp $camp, $role = null)
    {
        return new self($user, $camp, null, self::STATUS_REQUESTED, $role);
    }

    public static function createInvitation(User $user, Camp $camp, User $inviter, $role = null)
    {
        return new self($user, $camp, $inviter, self::STATUS_INVITED, $role);
    }

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $camp;

    /**
     * The status of this Collaboration
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $status;

    /**
     * The role, a user currently have in this camp
     * @ORM\Column(type="string", nullable=false)
     */
    private $role;

    /**
     * Id of the user who accepted the request
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $requestAcceptedBy;

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    private function setRole($role)
    {
        if (!in_array($role, array(self::ROLE_GUEST, self::ROLE_MEMBER, self::ROLE_MANAGER, self::ROLE_OWNER))) {
            throw new \Exception("[$role] is not a valid value for CampCollaboration.role");
        }
        $this->role = $role;
    }

    public function getStatus()
    {
        return $this->status;
    }

    private function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_REQUESTED, self::STATUS_INVITED, self::STATUS_ESTABLISHED))) {
            throw new \Exception("[$status] is not a valid value for CampCollaboration.status");
        }
        $this->status = $status;
    }

    private function setRequestAcceptedBy(User $user)
    {
        $this->requestAcceptedBy = $user->getId() . "::" . $user->getFullName();
    }

    /**
     * @return boolean
     */
    public function isGuest()
    {
        return $this->role == self::ROLE_GUEST;
    }

    /**
     * @return boolean
     */
    public function isMember()
    {
        return $this->role == self::ROLE_MEMBER;
    }

    /**
     * @return boolean
     */
    public function isManager()
    {
        return $this->role == self::ROLE_MANAGER;
    }

    /**
     * @return boolean
     */
    public function isOwner()
    {
        return $this->role == self::ROLE_OWNER;
    }

    /**
     * @return boolean
     */
    public function isEstablished()
    {
        return $this->status == self::STATUS_ESTABLISHED;
    }

    /**
     * @return boolean
     */
    public function isRequest()
    {
        return $this->status == self::STATUS_REQUESTED;
    }

    /**
     * @return boolean
     */
    public function isInvitation()
    {
        return $this->status == self::STATUS_INVITED;
    }

    /**
     * @throws \Exception
     */
    public function acceptInvitation()
    {
        if (! $this->isInvitation()) {
            throw new \Exception("Accept Invitation can only be called, if CampCollaboration is a Invitation");
        }

        $this->setStatus(self::STATUS_ESTABLISHED);
    }

    /**
     * @param  User       $user
     * @param  string     $role
     * @throws \Exception
     */
    public function acceptRequest(User $user, $role = null)
    {
        if (! $this->isRequest()) {
            throw new \Exception("Accept Request can only be called, if CampCollaboration is a Request");
        }

        $this->setRequestAcceptedBy($user);
        $this->setRole($role ?: $this->role);
        $this->setStatus(self::STATUS_ESTABLISHED);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->user->getList('collaborations')->removeElement($this);
        $this->camp->getList('collaborations')->removeElement($this);
    }

}
