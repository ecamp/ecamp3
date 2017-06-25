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
 * Connection between User and Group
 * User can send requests to Managers
 * Managers can send invitations to Users
 *
 * @ORM\Entity(repositoryClass="EcampCore\Repository\GroupMembershipRepository")
 * @ORM\Table(name="group_memberships", uniqueConstraints={@ORM\UniqueConstraint(name="user_group_unique",columns={"user_id","group_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class GroupMembership
    extends BaseEntity
{
    const ROLE_MEMBER  = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_UNRELATED      = 'unrelated';
    const STATUS_REQUESTED 		= 'requested';
    const STATUS_INVITED 		= 'invited';
    const STATUS_ESTABLISHED 	= 'established';

    public function __construct(User $user, Group $group, User $inviter = null, $status, $role)
    {
        parent::__construct();

        $this->user  = $user;
        $this->group = $group;

        $this->setStatus($status);
        $this->setRole($role ?: self::ROLE_MEMBER);

        if ($this->isInvitation()) {
            $this->setRequestAcceptedBy($inviter);
        } else {
            $this->requestAcceptedBy = null;
        }
    }

    public static function createRequest(User $user, Group $group, $role = null)
    {
        return new self($user, $group, null, self::STATUS_REQUESTED, $role);
    }

    public static function createInvitation(User $user, Group $group, User $inviter, $role = null)
    {
        return new self($user, $group, $inviter, self::STATUS_INVITED, $role);
    }

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group;

    /**
     * The status of this Membership
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $status;

    /**
     * The role, a user currently have in this group
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
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
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

    public function setRole($role)
    {
        if (!in_array($role, array(self::ROLE_MEMBER, self::ROLE_MANAGER))) {
            throw new \Exception("[$role] is not a valid value for GroupMembership.role");
        }
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    private function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_REQUESTED, self::STATUS_INVITED, self::STATUS_ESTABLISHED))) {
            throw new \Exception("[$status] is not a valid value for GroupMembership.status");
        }
        $this->status = $status;
    }

    private function setRequestAcceptedBy(User $user)
    {
        $this->requestAcceptedBy = $user->getId() . "::" . $user->getFullName();
    }

    /**
     * @return strint
     */
    public function getRoleName()
    {
        switch ($this->role) {

            case self::ROLE_MEMBER:
                return "Member";

            case self::ROLE_MANAGER:
                return "Manager";
        }
    }

    /**
     * True if the role is member or manager
     * @return boolean
     */
    public function isMember()
    {
        return $this->role == self::ROLE_MEMBER;
    }

    /**
     * True if the role is manager
     * @return boolean
     */
    public function isManager()
    {
        return $this->role == self::ROLE_MANAGER;
    }

    /**
     * True if the request/invitation is still open
     * @return boolean
     */
    public function isEstablished()
    {
        return $this->status == self::STATUS_ESTABLISHED;
    }

    /**
     * True if the user sent this request to a manager and the request is still open
     * @return boolean
     */
    public function isRequest()
    {
        return $this->status == self::STATUS_REQUESTED;
    }

    /**
     * True if a manager has sent this invitation to a user and the invitation is still open
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
            throw new \Exception("Accept Invitation can only be called, if GroupMembership is a Invitation");
        }

        $this->setStatus(self::STATUS_ESTABLISHED);
    }

    /**
     * @param  User       $manager
     * @param  string     $role
     * @throws \Exception
     */
    public function acceptRequest(User $manager, $role = null)
    {
        if (! $this->isRequest()) {
            throw new \Exception("Accept Request can only be called, if GroupMembership is a Request");
        }

        $this->setRequestAcceptedBy($manager);
        $this->setRole($role ?: $this->role);
        $this->setStatus(self::STATUS_ESTABLISHED);
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();

        $this->user->addToList('memberships', $this);
        $this->group->addToList('memberships', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->user->removeFromList('memberships', $this);
        $this->group->removeFromList('memberships', $this);
    }

}
