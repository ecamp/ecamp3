<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * CampTemplate.
 *
 * @ORM\Entity
 */
class CampTemplate extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="CategoryTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private Collection $categoryTemplates;

    /**
     * @ORM\OneToMany(targetEntity="MaterialListTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private Collection $materialListTemplates;

    public function __construct() {
        parent::__construct();

        $this->categoryTemplates = new ArrayCollection();
        $this->materialListTemplates = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getCategoryTemplates(): Collection {
        return $this->categoryTemplates;
    }

    public function addCategoryTemplate(CategoryTemplate $categoryTemplate): void {
        $categoryTemplate->setCampTemplate($this);
        $this->categoryTemplates->add($categoryTemplate);
    }

    public function removeCategoryTemplate(CategoryTemplate $categoryTemplate): void {
        $categoryTemplate->setCampTemplate(null);
        $this->categoryTemplates->removeElement($categoryTemplate);
    }

    public function getMaterialListTemplates(): Collection {
        return $this->materialListTemplates;
    }

    public function addMaterialListTemplate(MaterialListTemplate $materialListTemplate): void {
        $materialListTemplate->setCampTemplate($this);
        $this->materialListTemplates->add($materialListTemplate);
    }

    public function removeMaterialListTemplate(MaterialListTemplate $materialListTemplate): void {
        $materialListTemplate->setCampTemplate(null);
        $this->materialListTemplates->removeElement($materialListTemplate);
    }
}
