<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\CampCollaborationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="inviteKey_unique", columns={"inviteKey"})
 * })
 */
class CampCollaboration extends BaseEntity implements BelongsToCampInterface {
    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_INVITED = 'invited';
    const STATUS_ESTABLISHED = 'established';
    const STATUS_INACTIVE = 'inactive';

    const VALID_STATUS = [
        self::STATUS_INVITED,
        self::STATUS_ESTABLISHED,
        self::STATUS_INACTIVE,
    ];

    /**
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    protected Collection $dayResponsibles;

    /**
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    protected Collection $activityResponsibles;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $inviteEmail = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $inviteKey = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

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
    private ?string $collaborationAcceptedBy = null;

    public function __construct() {
        parent::__construct();

        $this->activityResponsibles = new ArrayCollection();
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): void {
        $this->camp = $camp;
    }

    public function getInviteEmail(): ?string {
        return $this->inviteEmail;
    }

    public function setInviteEmail(?string $inviteEmail): void {
        $this->inviteEmail = $inviteEmail;
    }

    public function getInviteKey(): ?string {
        return $this->inviteKey;
    }

    public function setInviteKey(?string $inviteKey): void {
        $this->inviteKey = $inviteKey;
    }

    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @throws \Exception
     */
    public function setStatus(string $status): void {
        if (!in_array($status, self::VALID_STATUS)) {
            throw new \Exception('Invalid status: '.$status);
        }
        $this->status = $status;
    }

    public function isEstablished(): bool {
        return self::STATUS_ESTABLISHED === $this->status;
    }

    public function isInvitation(): bool {
        return self::STATUS_INVITED === $this->status;
    }

    public function isInactive(): bool {
        return self::STATUS_INACTIVE === $this->status;
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

    public function getCollaborationAcceptedBy(): ?string {
        return $this->collaborationAcceptedBy;
    }

    public function setCollaborationAcceptedBy(?string $collaborationAcceptedBy): void {
        $this->collaborationAcceptedBy = $collaborationAcceptedBy;
    }

    public function getActivityResponsibles(): Collection {
        return $this->activityResponsibles;
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible): void {
        $activityResponsible->setCampCollaboration($this);
        $this->activityResponsibles->add($activityResponsible);
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): void {
        $activityResponsible->setCampCollaboration(null);
        $this->activityResponsibles->removeElement($activityResponsible);
    }

    public function getDayResponsibles(): Collection {
        return $this->dayResponsibles;
    }

    public function addDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->setCampCollaboration($this);
        $this->dayResponsibles->add($dayResponsible);
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->setCampCollaboration(null);
        $this->dayResponsibles->removeElement($dayResponsible);
    }
}
