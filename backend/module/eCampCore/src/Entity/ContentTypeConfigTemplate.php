<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentTypeConfigTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="ActivityCategoryTemplate")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ActivityCategoryTemplate $activityCategoryTemplate = null;

    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    /**
     * ContentType should be present on each activitiy.
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $required = false;

    /**
     * Multiple instances on a single activitiy are resonable.
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $multiple = true;

    public function getActivityCategoryTemplate(): ?ActivityCategoryTemplate {
        return $this->activityCategoryTemplate;
    }

    public function setActivityCategoryTemplate(?ActivityCategoryTemplate $activityCategoryTemplate) {
        $this->activityCategoryTemplate = $activityCategoryTemplate;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType) {
        $this->contentType = $contentType;
    }

    public function getRequired(): bool {
        return $this->required;
    }

    public function setRequired(bool $required) {
        $this->required = $required;
    }

    public function getMultiple(): bool {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple) {
        $this->multiple = $multiple;
    }
}
