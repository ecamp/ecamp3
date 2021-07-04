<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="inviteKey_unique", columns={"inviteKey"})
 * })
 */
#[ApiResource]
class CampCollaboration extends BaseEntity implements BelongsToCampInterface {
    public const ROLE_GUEST = 'guest';
    public const ROLE_MEMBER = 'member';
    public const ROLE_MANAGER = 'manager';

    public const VALID_ROLES = [
        self::ROLE_GUEST,
        self::ROLE_MEMBER,
        self::ROLE_MANAGER,
    ];

    public const STATUS_INVITED = 'invited';
    public const STATUS_ESTABLISHED = 'established';
    public const STATUS_INACTIVE = 'inactive';

    public const VALID_STATUS = [
        self::STATUS_INVITED,
        self::STATUS_ESTABLISHED,
        self::STATUS_INACTIVE,
    ];

    /**
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    public Collection $dayResponsibles;

    /**
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    public Collection $activityResponsibles;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $inviteEmail = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    public ?string $inviteKey = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collaborations")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    public ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="collaborations")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Camp $camp = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[Assert\Choice(choices: self::VALID_STATUS)]
    public string $status = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[Assert\Choice(choices: self::VALID_ROLES)]
    public string $role = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $collaborationAcceptedBy = null;

    public function __construct() {
        $this->dayResponsibles = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
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
     * @return ActivityResponsible[]
     */
    public function getActivityResponsibles(): array {
        return $this->activityResponsibles->getValues();
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible): self {
        if (!$this->activityResponsibles->contains($activityResponsible)) {
            $this->activityResponsibles[] = $activityResponsible;
            $activityResponsible->campCollaboration = $this;
        }

        return $this;
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): self {
        if ($this->activityResponsibles->removeElement($activityResponsible)) {
            if ($activityResponsible->campCollaboration === $this) {
                $activityResponsible->campCollaboration = null;
            }
        }

        return $this;
    }

    /**
     * @return DayResponsible[]
     */
    public function getDayResponsibles(): array {
        return $this->dayResponsibles->getValues();
    }

    public function addDayResponsible(DayResponsible $dayResponsible): self {
        if (!$this->dayResponsibles->contains($dayResponsible)) {
            $this->dayResponsibles[] = $dayResponsible;
            $dayResponsible->campCollaboration = $this;
        }

        return $this;
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): self {
        if ($this->dayResponsibles->removeElement($dayResponsible)) {
            if ($dayResponsible->campCollaboration === $this) {
                $dayResponsible->campCollaboration = null;
            }
        }

        return $this;
    }
}
