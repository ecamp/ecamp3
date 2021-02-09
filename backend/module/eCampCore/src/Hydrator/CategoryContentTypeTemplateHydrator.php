<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class CategoryContentTypeTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'contentType' => Util::Entity(function (CategoryContentTypeTemplate $e) {
                return $e->getContentType();
            }),
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CategoryContentTypeTemplate $categoryContentTypeTemplate */
        $categoryContentTypeTemplate = $object;

        return [
            'id' => $categoryContentTypeTemplate->getId(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CategoryContentTypeTemplate {
        return $object;
    }
}
