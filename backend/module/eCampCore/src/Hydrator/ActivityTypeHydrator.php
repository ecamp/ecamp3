<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityType;
use eCampApi\V1\Rest\ActivityTypeContentType\ActivityTypeContentTypeCollection;
use Laminas\Hydrator\HydratorInterface;

class ActivityTypeHydrator implements HydratorInterface {
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
        /** @var ActivityType $activityType */
        $activityType = $object;

        return [
            'id' => $activityType->getId(),
            'name' => $activityType->getName(),
            'template' => $activityType->getTemplate(),
            'defaultColor' => $activityType->getDefaultColor(),
            'defaultNumberingStyle' => $activityType->getDefaultNumberingStyle(),
            'activityTypeContentTypes' => new ActivityTypeContentTypeCollection($activityType->getActivityTypeContentTypes()),
            //            'activityTypeFactories' => new ActivityTypeFactoryCollection($activityType->getActivityTypeFactories()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ActivityType $activityType */
        $activityType = $object;

        if (isset($data['name'])) {
            $activityType->setName($data['name']);
        }
        if (isset($data['defaultColor'])) {
            $activityType->setDefaultColor($data['defaultColor']);
        }
        if (isset($data['defaultNumberingStyle'])) {
            $activityType->setDefaultNumberingStyle($data['defaultNumberingStyle']);
        }

        return $activityType;
    }
}
