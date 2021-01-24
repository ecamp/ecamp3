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
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="ActivityCategoryTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private Collection $activityCategoryTemplates;

    /**
     * @ORM\OneToMany(targetEntity="MaterialListTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private Collection $materialListTemplates;

    public function __construct() {
        parent::__construct();

        $this->activityCategoryTemplates = new ArrayCollection();
        $this->materialListTemplates = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name) {
        $this->name = $name;
    }

    public function getActivityCategoryTemplates(): Collection {
        return $this->activityCategoryTemplates;
    }

    public function addActivityCategoryTemplate(ActivityCategoryTemplate $activityCategoryTemplate) {
        $activityCategoryTemplate->setCampTemplate($this);
        $this->activityCategoryTemplates->add($activityCategoryTemplate);
    }

    public function removeActivityCategoryTemplate(ActivityCategoryTemplate $activityCategoryTemplate) {
        $activityCategoryTemplate->setCampTemplate(null);
        $this->activityCategoryTemplates->removeElement($activityCategoryTemplate);
    }

    public function getMaterialListTemplates(): Collection {
        return $this->materialListTemplates;
    }

    public function addMaterialListTemplate(MaterialListTemplate $materialListTemplate) {
        $materialListTemplate->setCampTemplate($this);
        $this->materialListTemplates->add($materialListTemplate);
    }

    public function removeMaterialListTemplate(MaterialListTemplate $materialListTemplate) {
        $materialListTemplate->setCampTemplate(null);
        $this->materialListTemplates->removeElement($materialListTemplate);
    }
}
