<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Activity extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityContent", mappedBy="activity", cascade={"all"}, orphanRemoval=true)
     */
    protected $activityContents;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true)
     */
    protected $scheduleEntries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     */
    protected $activityResponsibles;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var ActivityCategory
     * @ORM\ManyToOne(targetEntity="ActivityCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityCategory;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $location = '';

    public function __construct() {
        parent::__construct();

        $this->activityContents = new ArrayCollection();
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
        $this->progress = 0;
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the Activity. Instead use {@see Camp::addActivity()}
     *
     * @param $camp
     */
    public function setCamp($camp) {
        $this->camp = $camp;
    }

    public function getActivityCategory(): ActivityCategory {
        return $this->activityCategory;
    }

    public function setActivityCategory(ActivityCategory $activityCategory): void {
        $this->activityCategory = $activityCategory;
    }

    /**
     * @return ActivityType
     */
    public function getActivityType() {
        return (null !== $this->activityCategory) ? $this->activityCategory->getActivityType() : null;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $location): void {
        $this->location = $location;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityContents() {
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

    /**
     * @return ArrayCollection
     */
    public function getScheduleEntries() {
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

    /**
     * @return ArrayCollection
     */
    public function getActivityResponsibles() {
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

    /** @ORM\PrePersist */
    public function PrePersist() {
        parent::PrePersist();

        if (null !== $this->getActivityType() && $this->getActivityContents()->isEmpty()) {
            $this->createDefaultActivityContents();
        }
    }

    /**
     * Replicates the default content-type structure given by the ActivityType.
     */
    public function createDefaultActivityContents(ContentTypeStrategyProvider $contentTypeStrategyProvider = null) {
        foreach ($this->getActivityType()->getActivityTypeContentTypes() as $activityTypeContentType) {
            for ($idx = 0; $idx < $activityTypeContentType->getDefaultInstances(); ++$idx) {
                /** @var ContentType $contentType */
                $contentType = $activityTypeContentType->getContentType();
                $contentTypeName = $contentType->getName().' ';
                $contentTypeName .= str_pad($idx + 1, 2, '0');

                $activityContent = new ActivityContent();
                $activityContent->setContentType($contentType);
                $activityContent->setInstanceName($contentTypeName);

                if ($contentTypeStrategyProvider) {
                    $activityContent->setContentTypeStrategyProvider($contentTypeStrategyProvider);
                }

                $this->addActivityContent($activityContent);
            }
        }
    }
}
