<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\CampRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="camps", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="owner_name_unique", columns={"owner_id", "name"})
 * })
 */
class Camp extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp", orphanRemoval=true)
     */
    protected $collaborations;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="camp", orphanRemoval=true)
     */
    protected $jobs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp", orphanRemoval=true)
     * @ORM\OrderBy({"start": "ASC"})
     */
    protected $periods;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventCategory", mappedBy="camp", orphanRemoval=true)
     */
    protected $eventCategories;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="camp", orphanRemoval=true)
     */
    protected $events;

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

    public function __construct() {
        parent::__construct();

        $this->collaborations = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->eventCategories = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * @return CampType
     */
    public function getCampType() {
        return $this->campType;
    }

    public function setCampType(CampType $campType) {
        $this->campType = $campType;
    }

    /**
     * @param null $key
     *
     * @return object
     */
    public function getConfig($key = null) {
        return (null !== $this->campType) ? $this->campType->getConfig($key) : null;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    public function setTitle(string $title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMotto() {
        return $this->motto;
    }

    public function setMotto(string $motto) {
        $this->motto = $motto;
    }

    /**
     * @return User
     */
    public function getCreator() {
        return $this->creator;
    }

    public function setCreator(User $creator) {
        $this->creator = $creator;
    }

    /**
     * @return AbstractCampOwner
     */
    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        if (!$owner instanceof User) {
            throw new \Exception('Owner must be a user. Groups are not (yet) supported.');
        }

        $this->owner = $owner;
    }

    /**
     * @return bool
     */
    public function belongsToUser() {
        return $this->owner instanceof User;
    }

    /**
     * @return bool
     */
    public function belongsToGroup() {
        return $this->owner instanceof Group;
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

    public function getRole($userId) {
        $campCollaborations = $this->collaborations->filter(function (CampCollaboration $cc) use ($userId) {
            return $cc->getUser()->getId() == $userId;
        });

        if (1 == $campCollaborations->count()) {
            /** @var CampCollaboration $campCollaboration */
            $campCollaboration = $campCollaborations->first();
            if ($campCollaboration->isEstablished()) {
                return $campCollaboration->getRole();
            }
        }

        return CampCollaboration::ROLE_GUEST;
    }

    /**
     * @param string $userId
     *
     * @return bool
     */
    public function isCollaborator($userId) {
        if ($this->getCreator()->getId() == $userId) {
            return true;
        }
        if ($this->getOwner()->getId() == $userId) {
            return true;
        }

        return $this->getCampCollaborations()->exists(function ($idx, CampCollaboration $cc) use ($userId) {
            return $cc->isEstablished() && ($cc->getUser()->getId() == $userId);
        });
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
    public function getEvents() {
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

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this;
    }
}
