<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp", orphanRemoval=true)
     */
    protected Collection $collaborations;

    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="camp", orphanRemoval=true)
     */
    protected Collection $jobs;

    /**
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp", orphanRemoval=true)
     * @ORM\OrderBy({"start": "ASC"})
     */
    protected Collection $periods;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="camp", orphanRemoval=true)
     */
    protected Collection $categories;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="camp", orphanRemoval=true)
     */
    protected Collection $activities;

    /**
     * @ORM\OneToMany(targetEntity="MaterialList", mappedBy="camp", orphanRemoval=true)
     */
    protected Collection $materialLists;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $campTemplateId = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private ?string $motto = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $creator = null;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractCampOwner", inversedBy="ownedCamps")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?AbstractCampOwner $owner = null;

    public function __construct() {
        parent::__construct();

        $this->collaborations = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->materialLists = new ArrayCollection();
    }

    public function getCampTemplateId(): ?string {
        return $this->campTemplateId;
    }

    public function setCampTemplateId(?string $campTemplateId) {
        $this->campTemplateId = $campTemplateId;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name) {
        $this->name = $name;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title) {
        $this->title = $title;
    }

    public function getMotto(): ?string {
        return $this->motto;
    }

    public function setMotto(?string $motto) {
        $this->motto = $motto;
    }

    public function getCreator(): ?User {
        return $this->creator;
    }

    public function setCreator(?User $creator) {
        $this->creator = $creator;
    }

    public function getOwner(): ?AbstractCampOwner {
        return $this->owner;
    }

    public function setOwner(?AbstractCampOwner $owner) {
        if (!$owner instanceof User) {
            throw new \Exception('Owner must be a user. Groups are not (yet) supported.');
        }

        $this->owner = $owner;
    }

    public function belongsToUser(): bool {
        return $this->owner instanceof User;
    }

    public function belongsToGroup(): bool {
        return $this->owner instanceof Group;
    }

    public function getCampCollaborations(): Collection {
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

    public function getRole($userId): string {
        if ($this->getOwner() && $this->getOwner()->getId() === $userId) {
            return CampCollaboration::ROLE_MANAGER;
        }

        $campCollaborations = $this->collaborations->filter(function (CampCollaboration $cc) use ($userId) {
            return null != $cc->getUser() && $cc->getUser()->getId() == $userId;
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
     */
    public function isCollaborator($userId): bool {
        if ($this->getCreator() && $this->getCreator()->getId() == $userId) {
            return true;
        }
        if ($this->getOwner() && $this->getOwner()->getId() == $userId) {
            return true;
        }

        return $this->getCampCollaborations()->exists(function ($idx, CampCollaboration $cc) use ($userId) {
            return $cc->isEstablished() && null != $cc->getUser() && ($cc->getUser()->getId() == $userId);
        });
    }

    public function getJobs(): Collection {
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

    public function getPeriods(): Collection {
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

    public function getCategories(): Collection {
        return $this->categories;
    }

    public function addCategory(Category $category): void {
        $category->setCamp($this);
        $this->categories->add($category);
    }

    public function removeCategory(Category $category): void {
        $category->setCamp(null);
        $this->categories->removeElement($category);
    }

    public function getActivities(): Collection {
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

    public function getMaterialLists(): Collection {
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

    public function getCamp(): ?Camp {
        return $this;
    }
}
