<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class ActivityCategoryHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'activityType' => Util::Entity(function (ActivityCategory $ec) {
                return $ec->getActivityType();
            }),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $object;

        return [
            'id' => $activityCategory->getId(),
            'short' => $activityCategory->getShort(),
            'name' => $activityCategory->getName(),

            'color' => $activityCategory->getColor(),
            'numberingStyle' => $activityCategory->getNumberingStyle(),

            'camp' => EntityLink::Create($activityCategory->getCamp()),
            'activityType' => EntityLink::Create($activityCategory->getActivityType()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $object;

        if (isset($data['camp'])) {
            $activityCategory->setCamp($data['camp']);
        }
        if (isset($data['activityType'])) {
            $activityCategory->setActivityType($data['activityType']);
        }

        if (isset($data['short'])) {
            $activityCategory->setShort($data['short']);
        }
        if (isset($data['name'])) {
            $activityCategory->setName($data['name']);
        }

        if (isset($data['color'])) {
            $activityCategory->setColor($data['color']);
        }
        if (isset($data['numberingStyle'])) {
            $activityCategory->setNumberingStyle($data['numberingStyle']);
        }

        return $activityCategory;
    }
}
