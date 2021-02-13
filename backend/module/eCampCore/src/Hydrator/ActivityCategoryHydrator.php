<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Lib\Entity\EntityLink;
use eCampApi\V1\Rest\ContentTypeConfig\ContentTypeConfigCollection;
use Laminas\Hydrator\HydratorInterface;

class ActivityCategoryHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $object;

        return [
            'id' => $activityCategory->getId(),
            'short' => $activityCategory->getShort(),
            'name' => $activityCategory->getName(),

            'color' => $activityCategory->getColor(),
            'numberingStyle' => $activityCategory->getNumberingStyle(),

            'camp' => EntityLink::Create($activityCategory->getCamp()),
            'contentTypeConfigs' => new ContentTypeConfigCollection($activityCategory->getContentTypeConfigs()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ActivityCategory {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $object;

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
