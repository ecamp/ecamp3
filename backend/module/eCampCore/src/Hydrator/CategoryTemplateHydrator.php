<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CategoryTemplate;
use eCampApi\V1\Rest\ContentTypeConfigTemplate\ContentTypeConfigTemplateCollection;
use Laminas\Hydrator\HydratorInterface;

class CategoryTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CategoryTemplate $categoryTemplate */
        $categoryTemplate = $object;

        return [
            'id' => $categoryTemplate->getId(),
            'short' => $categoryTemplate->getShort(),
            'name' => $categoryTemplate->getName(),
            'color' => $categoryTemplate->getColor(),
            'numberingStyle' => $categoryTemplate->getNumberingStyle(),
            'contentTypeConfigTemplates' => new ContentTypeConfigTemplateCollection($categoryTemplate->getContentTypeConfigTemplates()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CategoryTemplate {
        /** @var CategoryTemplate $categoryTemplate */
        $categoryTemplate = $object;

        if (isset($data['short'])) {
            $categoryTemplate->setShort($data['short']);
        }
        if (isset($data['name'])) {
            $categoryTemplate->setName($data['name']);
        }
        if (isset($data['color'])) {
            $categoryTemplate->setColor($data['color']);
        }
        if (isset($data['numberingStyle'])) {
            $categoryTemplate->setNumberingStyle($data['numberingStyle']);
        }

        return $categoryTemplate;
    }
}
