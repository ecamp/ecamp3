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
    private $activityCategoryTemplate;

    public function __construct() {
        parent::__construct();

        $this->activityCategoryTemplate = new ArrayCollection();
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
    public function getActivityCategoryTemplate() {
        return $this->activityCategoryTemplate;
    }

    public function addActivityCategoryTemplate(ActivityCategoryTemplate $activityCategoryTemplate) {
        $activityCategoryTemplate->setCampTemplate($this);
        $this->activityCategoryTemplate->add($activityCategoryTemplate);
    }

    public function removeActivityCategoryTemplate(ActivityCategoryTemplate $activityCategoryTemplate) {
        $activityCategoryTemplate->setCampTemplate(null);
        $this->activityCategoryTemplate->removeElement($activityCategoryTemplate);
    }
}
