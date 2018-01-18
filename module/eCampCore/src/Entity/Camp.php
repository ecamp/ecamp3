<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="camps", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="owner_name_unique", columns={"owner_id", "name"})
 * })
 */
class Camp extends BaseEntity
{
    public function __construct() {
        parent::__construct();

        $this->collaborations = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->eventCategories = new ArrayCollection();
        $this->events = new ArrayCollection();
    }


    /**
     * @var CampType
     * @ORM\ManyToOne(targetEntity="CampType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campType;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $motto;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     */
    private $creator;

    /**
     * @var AbstractCampOwner
     * @ORM\ManyToOne(targetEntity="AbstractCampOwner", inversedBy="ownedCamps")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    private $owner;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp")
     */
    protected $collaborations;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="camp")
     */
    protected $jobs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp")
     * @ORM\OrderBy({"start" = "ASC"})
     */
    protected $periods;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventCategory", mappedBy="camp")
     */
    protected $eventCategories;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="camp")
     */
    protected $events;


    /**
     * @return CampType
     */
    public function getCampType() {
        return $this->campType;
    }

    public function setCampType(CampType $campType): void {
        $this->campType = $campType;
    }

    /**
     * @return object
     */
    public function getCampTypeConfig() {
        return ($this->campType !== null) ? $this->campType->getConfig() : null;
    }


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getMotto(): string {
        return $this->motto;
    }

    public function setMotto(string $motto): void {
        $this->motto = $motto;
    }


    /**
     * @return User
     */
    public function getCreator(): User {
        return $this->creator;
    }

    public function setCreator(User $creator): void {
        $this->creator = $creator;
    }


    /**
     * @return AbstractCampOwner
     */
    public function getOwner(): AbstractCampOwner {
        return $this->owner;
    }

    public function setOwner(AbstractCampOwner $owner): void {
        $this->owner = $owner;
    }


    /**
     * @return ArrayCollection
     */
    public function getCampCollaborations() {
        return $this->collaborations;
    }

    public function addCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setCamp($this);
        $this->collaborations->add($collaboration);
    }

    public function removeCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setCamp(null);
        $this->collaborations->removeElement($collaboration);
    }


    /**
     * @return ArrayCollection
     */
    public function getJobs() {
        return $this->jobs;
    }

    public function addJob(Job $job) {
        $job->setCamp($this);
        $this->jobs->add($job);
    }

    public function removeJob(Job $job) {
        $job->setCamp(null);
        $this->jobs->removeElement($job);
    }


    /**
     * @return ArrayCollection
     */
    public function getPeriods() {
        return $this->periods;
    }

    public function addPeriod(Period $period): void {
        $period->setCamp($this);
        $this->periods->add($period);
    }

    public function removePeriod(Period $period): void {
        $period->setCamp(null);
        $this->periods->removeElement($period);
    }


    /**
     * @return ArrayCollection
     */
    public function getEventCategories() {
        return $this->eventCategories;
    }

    public function addEventCategory(EventCategory $eventCategory): void {
        $eventCategory->setCamp($this);
        $this->eventCategories->add($eventCategory);
    }

    public function removeEventCategory(EventCategory $eventCategory): void {
        $eventCategory->setCamp(null);
        $this->eventCategories->removeElement($eventCategory);
    }


    /**
     * @return ArrayCollection
     */
    public function getEvents(): ArrayCollection {
        return $this->events;
    }

    public function addEvent(Event $event): void {
        $event->setCamp($this);
        $this->events->add($event);
    }

    public function removeEvent(Event $event): void {
        $event->setCamp(null);
        $this->events->removeElement($event);
    }

}
