<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ActivityContent;

interface ContentTypeStrategyInterface {
    public function activityContentExtract(ActivityContent $activityContent): array;

    public function activityContentCreated(ActivityContent $activityContent): void;
}
