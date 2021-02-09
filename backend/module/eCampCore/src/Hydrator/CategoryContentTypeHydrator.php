<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CategoryContentType;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class CategoryContentTypeHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'contentType' => Util::Entity(function (CategoryContentType $e) {
                return $e->getContentType();
            }),
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CategoryContentType $categoryContentType */
        $categoryContentType = $object;

        return [
            'id' => $categoryContentType->getId(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CategoryContentType {
        return $object;
    }
}
