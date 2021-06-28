<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
#[ApiResource]
class Activity extends AbstractContentNodeOwner implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true)
     *
     * @var ScheduleEntry[]
     */
    public $scheduleEntries;

    /**
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     *
     * @var ActivityResponsible[]
     */
    public $activityResponsibles;

    /**
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Camp $camp = null;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?Category $category = null;

    /**
     * @ORM\Column(type="text")
     */
    public ?string $title = null;

    /**
     * @ORM\Column(type="text")
     */
    public string $location = '';

    public function __construct() {
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function getScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->activity = $this;
        $this->scheduleEntries->add($scheduleEntry);
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->activity = null;
        $this->scheduleEntries->removeElement($scheduleEntry);
    }

    public function getActivityResponsibles(): array {
        return $this->activityResponsibles->getValues();
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible): void {
        $activityResponsible->activity = $this;
        $this->activityResponsibles->add($activityResponsible);
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): void {
        $activityResponsible->activity = null;
        $this->activityResponsibles->removeElement($activityResponsible);
    }
}
