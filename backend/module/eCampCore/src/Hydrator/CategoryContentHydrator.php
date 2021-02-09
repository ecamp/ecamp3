<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CategoryContent;
use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class CategoryContentHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CategoryContent $categoryContent */
        $categoryContent = $object;
        $contentType = $categoryContent->getContentType();

        return [
            'id' => $categoryContent->getId(),
            'instanceName' => $categoryContent->getInstanceName(),
            'contentTypeName' => $contentType->getName(),

            'contentType' => new EntityLink($categoryContent->getContentType()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CategoryContent {
        /** @var CategoryContent $categoryContent */
        $categoryContent = $object;

        if (isset($data['instanceName'])) {
            $categoryContent->setInstanceName($data['instanceName']);
        }

        return $categoryContent;
    }
}
