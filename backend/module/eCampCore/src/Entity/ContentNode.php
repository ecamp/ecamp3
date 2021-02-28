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
class ContentNode extends BaseEntity implements BelongsToCampInterface, ContentTypeStrategyProviderAware {
    use ContentTypeStrategyProviderTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractContentNodeOwner", inversedBy="contentNodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?AbstractContentNodeOwner $owner = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ContentNode $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $slot = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?float $position = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?string $config = null;

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

    public function getOwner(): ?AbstractContentNodeOwner {
        return $this->owner;
    }

    public function setOwner(?AbstractContentNodeOwner $owner): void {
        $this->owner = $owner;
    }

    public function getCamp(): ?Camp {
        return (null != $this->owner) ? $this->owner->getCamp() : null;
    }

    public function isRoot(): bool {
        return null == $this->parent;
    }

    public function getParent(): ?ContentNode {
        return $this->parent;
    }

    public function setParent(?ContentNode $parent): void {
        $origParentOwner = isset($this->parent) ? $this->parent->getOwner() : null;
        $newParentOwner = isset($parent) ? $parent->getOwner() : null;

        if ($origParentOwner != $newParentOwner) {
            $this->setOwner($newParentOwner);
        }
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

    public function addChild(ContentNode $contentNode): void {
        $contentNode->setParent($this);
        $this->children->add($contentNode);
    }

    public function removeChild(ContentNode $contentNode): void {
        $contentNode->setParent(null);
        $this->children->removeElement($contentNode);
    }

    public function getSlot(): ?string {
        return $this->slot;
    }

    public function setSlot(?string $slot): void {
        $this->slot = $slot;
    }

    public function getPosition(): ?int {
        return $this->position;
    }

    public function setPosition(?int $position): void {
        $this->position = $position;
    }

    public function getConfig() {
        return $this->config;
    }

    public function setConfig($config): void {
        $this->config = $config;
    }

    /**
     * Returns the strategy class of the content-type.
     */
    public function getContentTypeStrategy(): ContentTypeStrategyInterface {
        return $this->getContentTypeStrategyProvider()->get($this->getContentType());
    }

    /** @ORM\PrePersist */
    public function PrePersist(): void {
        $this->getContentTypeStrategy()->contentNodeCreated($this);
    }
}
