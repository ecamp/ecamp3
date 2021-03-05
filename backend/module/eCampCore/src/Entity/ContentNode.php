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
class ContentNode extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToOne(targetEntity="AbstractContentNodeOwner", mappedBy="rootContentNode")
     */
    private ?AbstractContentNodeOwner $owner = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode")
     * @ORM\JoinColumn(nullable=true)
     */
    private ContentNode $root;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="root")
     */
    private Collection $allChildren;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ContentNode $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="parent")
     */
    private Collection $myChildren;

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

        $this->root = $this;
        $this->myChildren = new ArrayCollection();
        $this->allChildren = new ArrayCollection();
    }

    public function getOwner(): ?AbstractContentNodeOwner {
        return $this->owner;
    }

    public function setOwner(?AbstractContentNodeOwner $owner): void {
        $this->owner = $owner;
    }

    public function getCamp(): ?Camp {
        $root = $this->getRoot();
        $owner = $root->getOwner();

        if ($owner instanceof BelongsToCampInterface) {
            return $owner->getCamp();
        }

        return null;
    }

    public function getMyChildren(): Collection {
        return $this->myChildren;
    }

    public function getAllChildren(): Collection {
        return $this->allChildren;
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

    public function isRoot(): bool {
        return null == $this->parent;
    }

    public function getRoot(): ContentNode {
        return $this->root;
    }

    public function getParent(): ?ContentNode {
        return $this->parent;
    }

    public function setParent(?ContentNode $parent) {
        // if different parent, update parent
        if ($this->parent !== $parent) {
            $root = (null != $parent) ? $parent->getRoot() : $this;
            $this->setRoot($root);

            // remove me from old parent
            if (null != $this->parent) {
                $this->parent->myChildren->removeElement($this);
            }

            // update parent
            $this->parent = $parent;

            // add me to the new parent
            if (null != $this->parent) {
                $this->parent->myChildren->add($this);
            }
        }
    }

    private function setRoot(ContentNode $root) {
        // if different root, update root
        if ($this->root !== $root) {
            // remove me from old root
            $this->root->allChildren->removeElement($this);

            // update my root
            $this->root = $root;
            // update my children
            foreach ($this->myChildren as $child) {
                $child->setRoot($root);
            }

            // add me to the new root
            $this->root->allChildren->add($this);
        }
    }
}
