<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\MaterialList;
use Laminas\Hydrator\HydratorInterface;

class MaterialItemHydrator implements HydratorInterface {
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
        /** @var MaterialItem $materialItem */
        $materialItem = $object;

        return [
            'id' => $materialItem->getId(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var MaterialItem $materialItem */
        $materialItem = $object;

        // if (isset($data['name'])) {
        //     $materialItem->setName($data['name']);
        // }

        return $materialItem;
    }
}
