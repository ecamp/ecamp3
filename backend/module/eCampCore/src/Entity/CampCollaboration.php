<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_camp_unique", columns={"userId", "campId"})
 * })
 */
class CampCollaboration extends BaseEntity implements BelongsToCampInterface {
    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_UNRELATED = 'unrelated';
    const STATUS_REQUESTED = 'requested';
    const STATUS_INVITED = 'invited';
    const STATUS_ESTABLISHED = 'established';
    const STATUS_LEFT = 'leaved';

    const VALID_STATUS = [
        self::STATUS_INVITED,
        self::STATUS_REQUESTED,
        self::STATUS_ESTABLISHED,
        self::STATUS_LEFT,
    ];

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    protected $activityResponsibles;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $user;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

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
    private $collaborationAcceptedBy;

    public function __construct() {
        parent::__construct();

        $this->activityResponsibles = new ArrayCollection();
        $this->status = self::STATUS_UNRELATED;
        $this->role = self::ROLE_GUEST;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    public function setCamp($camp) {
        $this->camp = $camp;
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

    /**
     * @return string
     */
    public function getCollaborationAcceptedBy() {
        return $this->collaborationAcceptedBy;
    }

    public function setCollaborationAcceptedBy($collaborationAcceptedBy) {
        $this->collaborationAcceptedBy = $collaborationAcceptedBy;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityResponsibles() {
        return $this->activityResponsibles;
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setCampCollaboration($this);
        $this->activityResponsibles->add($activityResponsible);
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setCampCollaboration(null);
        $this->activityResponsibles->removeElement($activityResponsible);
    }

    /**
     * @ORM\PrePersist
     *
     * @throws \Exception
     */
    public function PrePersist() {
        parent::PrePersist();

        if (in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_UNRELATED])) {
            $this->collaborationAcceptedBy = null;
        }
    }

    /** @ORM\PreUpdate */
    public function PreUpdate() {
        parent::PreUpdate();
    }
}
