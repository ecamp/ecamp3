<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentTypeConfig extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="ActivityCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ActivityCategory $activityCategory = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $contentTypeConfigTemplateId = null;

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

    public function getCamp(): ?Camp {
        return (null != $this->activityCategory) ? $this->activityCategory->getCamp() : null;
    }

    public function getActivityCategory(): ?ActivityCategory {
        return $this->activityCategory;
    }

    public function setActivityCategory(?ActivityCategory $activityCategory) {
        $this->activityCategory = $activityCategory;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType) {
        $this->contentType = $contentType;
    }

    public function getContentTypeConfigTemplateId(): ?string {
        return $this->contentTypeConfigTemplateId;
    }

    public function setContentTypeConfigTemplateId(?string $contentTypeConfigTemplateId) {
        $this->contentTypeConfigTemplateId = $contentTypeConfigTemplateId;
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
