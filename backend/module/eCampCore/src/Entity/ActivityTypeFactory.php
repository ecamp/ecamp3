<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityTypeFactory.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="activitytype_name_unique", columns={"activityTypeId", "name"})
 * })
 */
class ActivityTypeFactory extends BaseEntity {
    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $factoryName;

    /**
     * @var ActivityType
     * @ORM\ManyToOne(targetEntity="ActivityType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityType;

    public function __construct() {
        parent::__construct();
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getFactoryName(): string {
        return $this->factoryName;
    }

    public function setFactoryName(string $factoryName): void {
        $this->factoryName = $factoryName;
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
}
