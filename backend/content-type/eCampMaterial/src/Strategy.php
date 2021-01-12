<?php

namespace eCamp\ContentType\Material;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;

class Strategy extends ContentTypeStrategyBase {
    public function activityContentExtract(ActivityContent $activityContent): array {
        return [];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
    }
}
