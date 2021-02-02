<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ContentType;
use Laminas\Hydrator\HydratorInterface;

class ContentTypeHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var ContentType $contentType */
        $contentType = $object;

        return [
            'id' => $contentType->getId(),
            'name' => $contentType->getName(),
            'active' => $contentType->getActive(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ContentType {
        /** @var ContentType $contentType */
        $contentType = $object;

        if (isset($data['name'])) {
            $contentType->setName($data['name']);
        }
        if (isset($data['active'])) {
            $contentType->setActive($data['active']);
        }

        return $contentType;
    }
}
