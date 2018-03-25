<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="group_memberships", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="user_group_unique", columns={"user_id","group_id"})
 * })
 */
class GroupMembership extends BaseEntity
{
    const ROLE_GUEST   = 'guest';
    const ROLE_MEMBER  = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_UNRELATED      = 'unrelated';
    const STATUS_REQUESTED 		= 'requested';
    const STATUS_INVITED 		= 'invited';
    const STATUS_ESTABLISHED 	= 'established';


    public function __construct() {
        parent::__construct();

        $this->status = self::STATUS_UNRELATED;
        $this->role = self::ROLE_GUEST;
    }

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $membershipAcceptedBy;



    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }


    /**
     * @return Group
     */
    public function getGroup() {
        return $this->group;
    }

    public function setGroup($group) {
        $this->group = $group;
    }


    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }
    /**
     * @param string $status
     * @throws \Exception
     */
    public function setStatus(string $status): void {
        if (!in_array($status, [self::STATUS_INVITED, self::STATUS_REQUESTED, self::STATUS_ESTABLISHED])) {
            throw new \Exception('Invalid status: ' . $status);
        }
        $this->status = $status;
    }

    public function isEstablished(): bool {
        return ($this->status === self::STATUS_ESTABLISHED);
    }

    public function isRequest(): bool {
        return ($this->status === self::STATUS_REQUESTED);
    }

    public function isInvitation(): bool {
        return ($this->status === self::STATUS_INVITED);
    }


    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }
    /**
     * @param string $role
     * @throws \Exception
     */
    public function setRole(string $role): void {
        if (!in_array($role, [self::ROLE_GUEST, self::ROLE_MEMBER, self::ROLE_MANAGER])) {
            throw new \Exception('Invalid role: ' . $role);
        }
        $this->role = $role;
    }

    public function isGuest(): bool {
        return ($this->role === self::ROLE_GUEST);
    }

    public function isMember(): bool {
        return ($this->role === self::ROLE_MEMBER);
    }

    public function isManager(): bool {
        return ($this->role === self::ROLE_MANAGER);
    }


    /**
     * @return string
     */
    public function getMembershipAcceptedBy() {
        return $this->membershipAcceptedBy;
    }

    public function setMembershipAcceptedBy($membershipAcceptedBy) {
        $this->membershipAcceptedBy = $membershipAcceptedBy;
    }


    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function PrePersist() {
        parent::PrePersist();

        if (in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_UNRELATED])) {
            $this->membershipAcceptedBy = null;
        }
    }

    /** @ORM\PreUpdate */
    public function PreUpdate() {
        parent::PreUpdate();
    }

}
