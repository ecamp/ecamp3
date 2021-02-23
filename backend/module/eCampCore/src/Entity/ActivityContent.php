<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProviderAware;
use eCamp\Core\ContentType\ContentTypeStrategyProviderTrait;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ActivityContent extends BaseEntity implements ContentTypeStrategyProviderAware, BelongsToCampInterface {
    use ContentTypeStrategyProviderTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Activity $activity = null;

    /**
     * @ORM\ManyToOne(targetEntity="ActivityContent")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ActivityContent $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="ActivityContent", mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?string $position = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $instanceName = null;

    public function __construct() {
        parent::__construct();

        $this->children = new ArrayCollection();
    }

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): void {
        $this->activity = $activity;
    }

    public function getCamp(): ?Camp {
        return (null != $this->activity) ? $this->activity->getCamp() : null;
    }

    public function isRoot(): bool {
        return null == $this->parent;
    }

    public function getParent(): ?ActivityContent {
        return $this->parent;
    }

    public function setParent(?ActivityContent $parent): void {
        $this->parent = $parent;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): void {
        $this->contentType = $contentType;
    }

    public function getInstanceName(): ?string {
        return $this->instanceName;
    }

    public function setInstanceName(?string $instanceName): void {
        $this->instanceName = $instanceName;
    }

    public function getChildren(): Collection {
        return $this->children;
    }

    public function addChild(ActivityContent $activityContent): void {
        $activityContent->setParent($this);
        $this->children->add($activityContent);
    }

    public function removeChild(ActivityContent $activityContent): void {
        $activityContent->setParent(null);
        $this->children->removeElement($activityContent);
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position): void {
        $this->position = $position;
    }

    /**
     * Returns the strategy class of the content-type.
     */
    public function getContentTypeStrategy(): ContentTypeStrategyInterface {
        return $this->getContentTypeStrategyProvider()->get($this->getContentType());
    }

    /** @ORM\PrePersist */
    public function PrePersist(): void {
        $this->getContentTypeStrategy()->activityContentCreated($this);
    }
}
