<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Annotation\EntityFilter;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\CampRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="owner_name_unique", columns={"ownerId", "name"})
 * })
 * @EntityFilter(filterClass="eCamp\Core\EntityFilter\CampFilter")
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
     * @ORM\OneToMany(targetEntity="ActivityCategory", mappedBy="camp", orphanRemoval=true)
     */
    protected $activityCategories;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="camp", orphanRemoval=true)
     */
    protected $activities;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MaterialList", mappedBy="camp", orphanRemoval=true)
     */
    protected $materialLists;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $campTemplateId;

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @var AbstractCampOwner
     * @ORM\ManyToOne(targetEntity="AbstractCampOwner", inversedBy="ownedCamps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function __construct() {
        parent::__construct();

        $this->collaborations = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->activityCategories = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->materialLists = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCampTemplateId() {
        return $this->campTemplateId;
    }

    public function setCampTemplateId(string $campTemplateId) {
        $this->campTemplateId = $campTemplateId;
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
        if ($this->getOwner() && $this->getOwner()->getId() === $userId) {
            return CampCollaboration::ROLE_MANAGER;
        }

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
        if ($this->getCreator() && $this->getCreator()->getId() == $userId) {
            return true;
        }
        if ($this->getOwner() && $this->getOwner()->getId() == $userId) {
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
    public function getActivityCategories() {
        return $this->activityCategories;
    }

    public function addActivityCategory(ActivityCategory $activityCategory): void {
        $activityCategory->setCamp($this);
        $this->activityCategories->add($activityCategory);
    }

    public function removeActivityCategory(ActivityCategory $activityCategory): void {
        $activityCategory->setCamp(null);
        $this->activityCategories->removeElement($activityCategory);
    }

    /**
     * @return ArrayCollection
     */
    public function getActivities() {
        return $this->activities;
    }

    public function addActivity(Activity $activity): void {
        $activity->setCamp($this);
        $this->activities->add($activity);
    }

    public function removeActivity(Activity $activity): void {
        $activity->setCamp(null);
        $this->activities->removeElement($activity);
    }

    /**
     * @return ArrayCollection
     */
    public function getMaterialLists() {
        return $this->materialLists;
    }

    public function addMaterialList(MaterialList $materialList) {
        $materialList->setCamp($this);
        $this->materialLists->add($materialList);
    }

    public function removeMaterialList(MaterialList $materialList) {
        $materialList->setCamp(null);
        $this->materialLists->removeElement($materialList);
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this;
    }
}
