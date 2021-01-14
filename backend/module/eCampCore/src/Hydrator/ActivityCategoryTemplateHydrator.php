<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCampApi\V1\Rest\ContentTypeConfigTemplate\ContentTypeConfigTemplateCollection;
use Laminas\Hydrator\HydratorInterface;

class ActivityCategoryTemplateHydrator implements HydratorInterface {
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
        /** @var ActivityCategoryTemplate $activityCategoryTemplate */
        $activityCategoryTemplate = $object;

        return [
            'id' => $activityCategoryTemplate->getId(),
            'name' => $activityCategoryTemplate->getName(),
            'color' => $activityCategoryTemplate->getColor(),
            'numberingStyle' => $activityCategoryTemplate->getNumberingStyle(),
            'contentTypeConfigTemplates' => new ContentTypeConfigTemplateCollection($activityCategoryTemplate->getContentTypeConfigTemplates()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ActivityCategoryTemplate $activityCategoryTemplate */
        $activityCategoryTemplate = $object;

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
