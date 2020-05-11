<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Service\ServiceUtils;

abstract class ContentTypeStrategyBase implements ContentTypeStrategyInterface {
    /** @var ServiceUtils */
    private $serviceUtils;

    public function __construct(
        ServiceUtils $serviceUtils
    ) {
        $this->serviceUtils = $serviceUtils;
    }

    abstract public function activityContentExtract(ActivityContent $activityContent): array;

    abstract public function activityContentCreated(ActivityContent $activityContent): void;

    /**
     * @return ServiceUtils
     */
    protected function getServiceUtils() {
        return $this->serviceUtils;
    }
}
