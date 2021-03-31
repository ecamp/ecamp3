<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_group_unique", columns={"userId", "groupId"})
 * })
 */
class GroupMembership extends BaseEntity {
    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_UNRELATED = 'unrelated';
    const STATUS_REQUESTED = 'requested';
    const STATUS_INVITED = 'invited';
    const STATUS_ESTABLISHED = 'established';

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Group $group = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private string $status;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private string $role;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $membershipAcceptedBy = null;

    public function __construct() {
        parent::__construct();

        $this->status = self::STATUS_UNRELATED;
        $this->role = self::ROLE_GUEST;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }

    public function getGroup(): ?Group {
        return $this->group;
    }

    public function setGroup(?Group $group): void {
        $this->group = $group;
    }

    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @throws \Exception
     */
    public function setStatus(string $status): void {
        if (!in_array($status, [self::STATUS_INVITED, self::STATUS_REQUESTED, self::STATUS_ESTABLISHED])) {
            throw new \Exception('Invalid status: '.$status);
        }
        $this->status = $status;
    }

    public function isEstablished(): bool {
        return self::STATUS_ESTABLISHED === $this->status;
    }

    public function isRequest(): bool {
        return self::STATUS_REQUESTED === $this->status;
    }

    public function isInvitation(): bool {
        return self::STATUS_INVITED === $this->status;
    }

    public function getRole(): string {
        return $this->role;
    }

    /**
     * @throws \Exception
     */
    public function setRole(string $role): void {
        if (!in_array($role, [self::ROLE_GUEST, self::ROLE_MEMBER, self::ROLE_MANAGER])) {
            throw new \Exception('Invalid role: '.$role);
        }
        $this->role = $role;
    }

    public function isGuest(): bool {
        return self::ROLE_GUEST === $this->role;
    }

    public function isMember(): bool {
        return self::ROLE_MEMBER === $this->role;
    }

    public function isManager(): bool {
        return self::ROLE_MANAGER === $this->role;
    }

    public function getMembershipAcceptedBy(): ?string {
        return $this->membershipAcceptedBy;
    }

    public function setMembershipAcceptedBy(?string $membershipAcceptedBy): void {
        $this->membershipAcceptedBy = $membershipAcceptedBy;
    }

    /**
     * @ORM\PrePersist
     *
     * @throws \Exception
     */
    public function PrePersist(): void {
        parent::PrePersist();

        if (in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_UNRELATED])) {
            $this->membershipAcceptedBy = null;
        }
    }
}
