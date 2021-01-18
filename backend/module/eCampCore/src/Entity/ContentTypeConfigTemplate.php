<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentTypeConfigTemplate extends BaseEntity {
    /**
     * @var ActivityCategoryTemplate
     * @ORM\ManyToOne(targetEntity="ActivityCategoryTemplate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityCategoryTemplate;

    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contentType;

    /**
     * ContentType should be present on each activitiy.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $required = false;

    /**
     * Multiple instances on a single activitiy are resonable.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $multiple = true;

    /**
     * @return ActivityCategoryTemplate
     */
    public function getActivityCategoryTemplate() {
        return $this->activityCategoryTemplate;
    }

    public function setActivityCategoryTemplate(?ActivityCategoryTemplate $activityCategoryTemplate) {
        $this->activityCategoryTemplate = $activityCategoryTemplate;
    }

    /**
     * @return ContentType
     */
    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType) {
        $this->contentType = $contentType;
    }

    /**
     * @return bool
     */
    public function getRequired() {
        return $this->required;
    }

    public function setRequired(bool $required) {
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function getMultiple() {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple) {
        $this->multiple = $multiple;
    }
}
