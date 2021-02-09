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
class CategoryContentTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="CategoryTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Category $categoryTemplate = null;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryContentTemplate")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?CategoryContentTemplate $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="CategoryContentTemplate", mappedBy="parent")
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
        $this->children = new ArrayCollection();
    }

    public function getCategoryTemplate(): ?CategoryTemplate {
        return $this->categoryTemplate;
    }

    public function setCategoryTemplate(?CategoryTemplate $categoryTemplate): void {
        $this->categoryTemplate = $categoryTemplate;
    }

    public function getParent(): ?CategoryContentTemplate {
        return $this->parent;
    }

    public function setParent(?CategoryContentTemplate $parent): void {
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

    public function addChild(CategoryContentTemplate $categoryContentTemplate): void {
        $categoryContentTemplate->setParent($this);
        $this->children->add($categoryContentTemplate);
    }

    public function removeChild(CategoryContentTemplate $categoryContentTemplate): void {
        $categoryContentTemplate->setParent(null);
        $this->children->removeElement($categoryContentTemplate);
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position): void {
        $this->position = $position;
    }
}
