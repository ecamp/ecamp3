<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityCategoryTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private $activityCategoryTemplates;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MaterialListTemplate", mappedBy="campTemplate", orphanRemoval=true)
     */
    private $materialListTemplates;

    public function __construct() {
        parent::__construct();

        $this->activityCategoryTemplates = new ArrayCollection();
        $this->materialListTemplates = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getActivityCategoryTemplates() {
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

    /**
     * @return ArrayCollection
     */
    public function getMaterialListTemplates() {
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
