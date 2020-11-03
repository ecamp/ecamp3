<?php

namespace eCamp\Core\ContentType;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\BelongsToActivityContentInterface;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseContentTypeEntity extends BaseEntity implements BelongsToActivityContentInterface {
    /**
     * @var ActivityContent
     * @ORM\ManyToOne(targetEntity="eCamp\Core\Entity\ActivityContent")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $activityContent;

    /**
     * @return ActivityContent
     */
    public function getActivityContent() {
        return $this->activityContent;
    }

    public function setActivityContent(ActivityContent $activityContent) {
        $this->activityContent = $activityContent;
    }
}
