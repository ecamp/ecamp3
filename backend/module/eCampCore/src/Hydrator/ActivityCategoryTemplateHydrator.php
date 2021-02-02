<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCampApi\V1\Rest\ContentTypeConfigTemplate\ContentTypeConfigTemplateCollection;
use Laminas\Hydrator\HydratorInterface;

class ActivityCategoryTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var ActivityCategoryTemplate $activityCategoryTemplate */
        $activityCategoryTemplate = $object;

        return [
            'id' => $activityCategoryTemplate->getId(),
            'short' => $activityCategoryTemplate->getShort(),
            'name' => $activityCategoryTemplate->getName(),
            'color' => $activityCategoryTemplate->getColor(),
            'numberingStyle' => $activityCategoryTemplate->getNumberingStyle(),
            'contentTypeConfigTemplates' => new ContentTypeConfigTemplateCollection($activityCategoryTemplate->getContentTypeConfigTemplates()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ActivityCategoryTemplate {
        /** @var ActivityCategoryTemplate $activityCategoryTemplate */
        $activityCategoryTemplate = $object;

        if (isset($data['short'])) {
            $activityCategoryTemplate->setShort($data['short']);
        }
        if (isset($data['name'])) {
            $activityCategoryTemplate->setName($data['name']);
        }
        if (isset($data['color'])) {
            $activityCategoryTemplate->setColor($data['color']);
        }
        if (isset($data['numberingStyle'])) {
            $activityCategoryTemplate->setNumberingStyle($data['numberingStyle']);
        }

        return $activityCategoryTemplate;
    }
}
