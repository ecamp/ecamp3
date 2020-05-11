<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityTypeFactory;
use Zend\Hydrator\HydratorInterface;

class ActivityTypeFactoryHydrator implements HydratorInterface {
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
        /** @var ActivityTypeFactory $activityTypeFactory */
        $activityTypeFactory = $object;

        return [
            'id' => $activityTypeFactory->getId(),
            'name' => $activityTypeFactory->getName(),
            'activityType' => $activityTypeFactory->getActivityType(),
            'factoryName' => $activityTypeFactory->getFactoryName(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var ActivityTypeFactory $activityTypeFactory
        return $object;
    }
}
