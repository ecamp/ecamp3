<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Lib\Entity\EntityLink;
use Zend\Hydrator\HydratorInterface;

class ActivityTypeContentTypeHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var ActivityTypeContentType $activityTypeContentType */
        $activityTypeContentType = $object;

        return [
            'id' => $activityTypeContentType->getId(),
            'activityType' => new EntityLink($activityTypeContentType->getActivityType()),
            'contentType' => $activityTypeContentType->getContentType(),
            'minNumberContentTypeInstances' => $activityTypeContentType->getMinNumberContentTypeInstances(),
            'maxNumberContentTypeInstances' => $activityTypeContentType->getMaxNumberContentTypeInstances(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var ActivityTypeContentType $activityTypeContentType
        return $object;
    }
}
