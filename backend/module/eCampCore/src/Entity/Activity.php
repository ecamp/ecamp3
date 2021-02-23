<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Activity extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="ActivityContent", mappedBy="activity", cascade={"all"}, orphanRemoval=true)
     */
    protected Collection $activityContents;

    /**
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true)
     */
    protected Collection $scheduleEntries;

    /**
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     */
    protected Collection $activityResponsibles;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Category $category = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $location = '';

    public function __construct() {
        parent::__construct();

        $this->activityContents = new ArrayCollection();
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the Activity. Instead use {@see Camp::addActivity()}
     *
     * @param $camp
     */
    public function setCamp(?Camp $camp) {
        $this->camp = $camp;
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function setCategory(?Category $category): void {
        $this->category = $category;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(?string $location): void {
        $this->location = $location;
    }

    public function getActivityContents(): Collection {
        return $this->activityContents;
    }

    public function addActivityContent(ActivityContent $activityContent) {
        $activityContent->setActivity($this);
        $this->activityContents->add($activityContent);
    }

    public function removeActivityContent(ActivityContent $activityContent) {
        $activityContent->setActivity(null);
        $this->activityContents->removeElement($activityContent);
    }

    public function getScheduleEntries(): Collection {
        return $this->scheduleEntries;
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry) {
        $scheduleEntry->setActivity($this);
        $this->scheduleEntries->add($scheduleEntry);
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry) {
        $scheduleEntry->setActivity(null);
        $this->scheduleEntries->removeElement($scheduleEntry);
    }

    public function getActivityResponsibles(): Collection {
        return $this->activityResponsibles;
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setActivity($this);
        $this->activityResponsibles->add($activityResponsible);
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible) {
        $activityResponsible->setActivity(null);
        $this->activityResponsibles->removeElement($activityResponsible);
    }
}
