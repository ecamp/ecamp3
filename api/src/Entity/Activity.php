<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A piece of programme that will be carried out once or multiple times in a camp.
 *
 * @ORM\Entity()
 */
#[ApiResource]
class Activity extends AbstractContentNodeOwner implements BelongsToCampInterface {
    /**
     * The list of points in time when this activity's programme will be carried out.
     *
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/schedule_entries/1a2b3c4d"]')]
    public Collection $scheduleEntries;

    /**
     * The list of people that are responsible for planning or carrying out this activity.
     *
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/activity_responsibles/1a2b3c4d"]')]
    public Collection $activityResponsibles;

    /**
     * The camp to which this activity belongs.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[Assert\DisableAutoMapping] // camp is set in the DataPersister
    #[ApiProperty(writable: false, example: '/camps/1a2b3c4d')]
    public ?Camp $camp = null;

    /**
     * The category to which this activity belongs. The category determines color and numbering scheme
     * of the activity, and is used for marking similar activities. Must be in the same camp as the activity.
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ApiProperty(example: '/categories/1a2b3c4d')]
    #[AssertBelongsToSameCamp]
    public ?Category $category = null;

    /**
     * The title of this activity that is shown in the picasso.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(example: 'Sportolympiade')]
    public ?string $title = null;

    /**
     * The physical location where this activity's programme will be carried out.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(example: 'Spielwiese')]
    public string $location = '';

    public function __construct() {
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    #[ApiProperty(writable: false)]
    public function setRootContentNode(?ContentNode $rootContentNode) {
        // Overridden to add annotations
        parent::setRootContentNode($rootContentNode);
    }

    #[Assert\DisableAutoMapping]
    public function getRootContentNode(): ?ContentNode {
        // Getter is here to add annotations to parent class property
        return $this->rootContentNode;
    }

    /**
     * @return ScheduleEntry[]
     */
    public function getScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if (!$this->scheduleEntries->contains($scheduleEntry)) {
            $this->scheduleEntries[] = $scheduleEntry;
            $scheduleEntry->activity = $this;
        }

        return $this;
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if ($this->scheduleEntries->removeElement($scheduleEntry)) {
            if ($scheduleEntry->activity === $this) {
                $scheduleEntry->activity = null;
            }
        }

        return $this;
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
            $activityResponsible->activity = $this;
        }

        return $this;
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): self {
        if ($this->activityResponsibles->removeElement($activityResponsible)) {
            if ($activityResponsible->activity === $this) {
                $activityResponsible->activity = null;
            }
        }

        return $this;
    }
}
