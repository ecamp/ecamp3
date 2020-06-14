<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ContentType;
use Laminas\Hydrator\HydratorInterface;

class ContentTypeHydrator implements HydratorInterface {
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
        /** @var ContentType $contentType */
        $contentType = $object;

        return [
            'id' => $contentType->getId(),
            'name' => $contentType->getName(),
            'active' => $contentType->getActive(),
            'allowMultiple' => $contentType->getAllowMultiple(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ContentType $contentType */
        $contentType = $object;

        if (isset($data['name'])) {
            $contentType->setName($data['name']);
        }
        if (isset($data['active'])) {
            $contentType->setActive($data['active']);
        }
        if (isset($data['allowMultiple'])) {
            $contentType->setActive($data['allowMultiple']);
        }

        return $contentType;
    }
}
