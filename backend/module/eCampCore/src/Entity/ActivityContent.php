<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProviderAware;
use eCamp\Core\ContentType\ContentTypeStrategyProviderTrait;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ActivityContent extends BaseEntity implements ContentTypeStrategyProviderAware, BelongsToCampInterface {
    use ContentTypeStrategyProviderTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Activity $activity = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected ?ContentType $contentType = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $instanceName = null;

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    public function setActivity(?Activity $activity) {
        $this->activity = $activity;
    }

    public function getCamp(): ?Camp {
        return (null != $this->activity) ? $this->activity->getCamp() : null;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): void {
        $this->contentType = $contentType;
    }

    public function getInstanceName(): ?string {
        return $this->instanceName;
    }

    public function setInstanceName(?string $instanceName): void {
        $this->instanceName = $instanceName;
    }

    /**
     * Returns the strategy class of the content-type.
     */
    public function getContentTypeStrategy(): ContentTypeStrategyInterface {
        return $this->getContentTypeStrategyProvider()->get($this->getContentType());
    }

    /** @ORM\PrePersist */
    public function PrePersist() {
        $this->getContentTypeStrategy()->activityContentCreated($this);
    }
}
