<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityTypeContentType.
 *
 * @ORM\Entity
 */
class ActivityTypeContentType extends BaseEntity {
    /**
     * @var ActivityType
     * @ORM\ManyToOne(targetEntity="ActivityType")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $activityType;

    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contentType;

    /**
     * Number of activity content instances that are created by default with each activity.
     *
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $defaultInstances = 0;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return ActivityType
     */
    public function getActivityType() {
        return $this->activityType;
    }

    public function setActivityType($activityType) {
        $this->activityType = $activityType;
    }

    public function getContentType(): ContentType {
        return $this->contentType;
    }

    public function setContentType(ContentType $contentType): void {
        $this->contentType = $contentType;
    }

    public function getDefaultInstances(): int {
        return $this->defaultInstances;
    }

    public function setDefaultInstances(int $defaultInstances): void {
        $this->defaultInstances = $defaultInstances;
    }
}
