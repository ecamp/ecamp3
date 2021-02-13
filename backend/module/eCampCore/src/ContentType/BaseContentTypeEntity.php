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
     * @ORM\ManyToOne(targetEntity="eCamp\Core\Entity\ActivityContent")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected ActivityContent $activityContent;

    public function getActivityContent(): ActivityContent {
        return $this->activityContent;
    }

    public function setActivityContent(ActivityContent $activityContent) {
        $this->activityContent = $activityContent;
    }
}
