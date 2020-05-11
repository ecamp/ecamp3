<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;
use Zend\Json\Json;

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
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minNumberContentTypeInstances;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxNumberContentTypeInstances;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $jsonConfig;

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

    public function getMinNumberContentTypeInstances(): int {
        return $this->minNumberContentTypeInstances;
    }

    public function setMinNumberContentTypeInstances(int $minNumberContentTypeInstances): void {
        $this->minNumberContentTypeInstances = $minNumberContentTypeInstances;
    }

    public function getMaxNumberContentTypeInstances(): int {
        return $this->maxNumberContentTypeInstances;
    }

    public function setMaxNumberContentTypeInstances(int $maxNumberContentTypeInstances): void {
        $this->maxNumberContentTypeInstances = $maxNumberContentTypeInstances;
    }

    public function getJsonConfig(): string {
        return $this->jsonConfig;
    }

    public function setJsonConfig(string $jsonConfig): void {
        $this->jsonConfig = $jsonConfig;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key = null) {
        $config = null;
        if (null != $this->jsonConfig) {
            $config = Json::decode($this->jsonConfig);
            if (null != $key) {
                $config = $config->{$key};
            }
        }

        return $config;
    }
}
