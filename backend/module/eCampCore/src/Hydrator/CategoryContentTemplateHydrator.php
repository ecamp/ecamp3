<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class CategoryContentTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CategoryContentTemplate $categoryContentTemplate */
        $categoryContentTemplate = $object;
        $contentType = $categoryContentTemplate->getContentType();

        return [
            'id' => $categoryContentTemplate->getId(),
            'instanceName' => $categoryContentTemplate->getInstanceName(),
            'contentTypeName' => $contentType->getName(),

            'contentType' => new EntityLink($categoryContentTemplate->getContentType()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CategoryContentTemplate {
        /** @var CategoryContentTemplate $categoryContentTemplate */
        $categoryContentTemplate = $object;

        if (isset($data['instanceName'])) {
            $categoryContentTemplate->setInstanceName($data['instanceName']);
        }

        return $categoryContentTemplate;
    }
}
