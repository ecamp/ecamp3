<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityType.
 *
 * @ORM\Entity
 */
class CategoryTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="CampTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected ?CampTemplate $campTemplate = null;

    /**
     * @ORM\OneToMany(targetEntity="CategoryContentTypeTemplate", mappedBy="categoryTemplate", orphanRemoval=true)
     */
    private Collection $categoryContentTypeTemplates;

    /**
     * @ORM\OneToMany(targetEntity="CategoryContentTemplate", mappedBy="categoryTemplate", orphanRemoval=true)
     */
    private Collection $categoryContentTemplates;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private ?string $short = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private ?string $color = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private ?string $numberingStyle = null;

    public function __construct() {
        parent::__construct();

        $this->categoryContentTypeTemplates = new ArrayCollection();
        $this->categoryContentTemplates = new ArrayCollection();
    }

    public function getCampTemplate(): ?CampTemplate {
        return $this->campTemplate;
    }

    public function setCampTemplate(?CampTemplate $campTemplate): void {
        $this->campTemplate = $campTemplate;
    }

    public function getShort(): ?string {
        return $this->short;
    }

    public function setShort(?string $short): void {
        $this->short = $short;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): void {
        $this->color = $color;
    }

    public function getNumberingStyle(): ?string {
        return $this->numberingStyle;
    }

    public function setNumberingStyle(?string $numberingStyle): void {
        $this->numberingStyle = $numberingStyle;
    }

    public function getCategoryContentTypeTemplates(): Collection {
        return $this->categoryContentTypeTemplates;
    }

    public function addCategoryContentTypeTemplate(CategoryContentTypeTemplate $categoryContentTypeTemplate): void {
        $categoryContentTypeTemplate->setCategoryTemplate($this);
        $this->categoryContentTypeTemplates->add($categoryContentTypeTemplate);
    }

    public function removeCategoryContentTypeTemplate(CategoryContentTypeTemplate $categoryContentTypeTemplate): void {
        $categoryContentTypeTemplate->setCategoryTemplate(null);
        $this->categoryContentTypeTemplates->removeElement($categoryContentTypeTemplate);
    }

    public function getCategoryContentTemplates(): Collection {
        return $this->categoryContentTemplates;
    }

    public function getRootCategoryContentTemplates(): Collection {
        return $this->categoryContentTemplates->filter(fn (CategoryContentTemplate $cct) => $cct->isRoot());
    }

    public function addCategoryContentTemplate(CategoryContentTemplate $categoryContentTemplate): void {
        $categoryContentTemplate->setCategoryTemplate($this);
        $this->categoryContentTemplates->add($categoryContentTemplate);
    }

    public function removeCategoryContentTemplate(CategoryContentTemplate $categoryContentTemplate): void {
        $categoryContentTemplate->setCategoryTemplate(null);
        $this->categoryContentTemplates->removeElement($categoryContentTemplate);
    }
}
