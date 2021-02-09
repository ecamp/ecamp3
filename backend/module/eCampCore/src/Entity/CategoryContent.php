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
class CategoryContent extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Category $category = null;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryContent")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?CategoryContent $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="CategoryContent", mappedBy="parent")
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

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $categoryContentTemplateId = null;

    public function __construct() {
        parent::__construct();

        $this->children = new ArrayCollection();
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function setCategory(?Category $category): void {
        $this->category = $category;
    }

    public function getCamp(): ?Camp {
        return (null != $this->category) ? $this->category->getCamp() : null;
    }

    public function getParent(): ?CategoryContent {
        return $this->parent;
    }

    public function setParent(?CategoryContent $parent): void {
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

    public function addChild(CategoryContent $categoryContent): void {
        $categoryContent->setParent($this);
        $this->children->add($categoryContent);
    }

    public function removeChild(CategoryContent $categoryContent): void {
        $categoryContent->setParent(null);
        $this->children->removeElement($categoryContent);
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position): void {
        $this->position = $position;
    }

    public function getCategoryContentTemplateId(): ?string {
        return $this->categoryContentTemplateId;
    }

    public function setCategoryContentTemplateId(?string $categoryContentTemplateId): void {
        $this->categoryContentTemplateId = $categoryContentTemplateId;
    }
}
