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
 * Connection between User and Group
 * User can send requests to Managers
 * Managers can send invitations to Users
 * @ORM\Entity(repositoryClass="EcampCore\Repository\UserGroupRepository")
 * @ORM\Table(name="user_groups", uniqueConstraints={@ORM\UniqueConstraint(name="user_group_unique",columns={"user_id","group_id"})})
 */
class UserGroup
    extends BaseEntity
    implements BelongsToParentResource
{
    const ROLE_NONE    = 0;
    const ROLE_MEMBER  = 10;
    const ROLE_MANAGER = 20;

    public function __construct($user = null, $group = null)
    {
        parent::__construct();

        $this->role  = self::ROLE_NONE;
        $this->user  = $user;
        $this->group = $group;

        $this->invitationAccepted = false;
        $this->requestedRole = null;
        $this->requestAcceptedBy = null;
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
     * The role, a user currently have in this group
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * The role, a user requested or was invited to have in this group
     * null = no open request
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requestedRole;

    /**
     * Id of the user who accepted the request
     * null = request has not been accepted yet
     * automatically set if an invitation is sent by a manager
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $requestAcceptedBy;

    /**
     * True if the user has accepted the invitation
     * automatically set to true, if request is made by user
     * @ORM\Column(type="boolean")
     */
    private $invitationAccepted;

    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function getParentResource()
    {
        return $this->group;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return strint
     */
    public function getRoleName()
    {
        switch ($this->role) {
            case self::ROLE_NONE:
                return "No Member";

            case self::ROLE_MEMBER:
                return "Member";

            case self::ROLE_MANAGER:
                return "Manager";
        }
    }

    /**
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
     * @return boolean
     */
    public function isMember()
    {
        return $this->role != self::ROLE_NONE;
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
    public function isOpen()
    {
        return isset($this->requestedRole);
    }

    /**
     * True if the user sent this request to a manager and the request is still open
     * @return boolean
     */
    public function isOpenRequest()
    {
        return $this->isOpen() && !isset($this->requestAcceptedBy);
    }

    /**
     * True if a manager has sent this invitation to a user and the invitation is still open
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

        if ($this->requestAcceptedBy != null) {
            $this->accept();
        }

        return $this;
    }

    /**
     * Request is accepted by the given manager
     * @param $manager
     */
    public function acceptRequest(User $manager)
    {
        $this->requestAcceptedBy = $manager;

        if ($this->invitationAccepted) {
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

}
