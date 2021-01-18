<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentTypeConfig extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var ActivityCategory
     * @ORM\ManyToOne(targetEntity="ActivityCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityCategory;

    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contentType;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $contentTypeConfigTemplateId;

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
     * @return Camp
     */
    public function getCamp() {
        return $this->activityCategory->getCamp();
    }

    /**
     * @return ActivityCategory
     */
    public function getActivityCategory() {
        return $this->activityCategory;
    }

    public function setActivityCategory(?ActivityCategory $activityCategory) {
        $this->activityCategory = $activityCategory;
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
     * @return string
     */
    public function getContentTypeConfigTemplateId() {
        return $this->contentTypeConfigTemplateId;
    }

    public function setContentTypeConfigTemplateId(string $contentTypeConfigTemplateId) {
        $this->contentTypeConfigTemplateId = $contentTypeConfigTemplateId;
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
