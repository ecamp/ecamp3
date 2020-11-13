<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\MaterialList;
use Laminas\Hydrator\HydratorInterface;

class MaterialListHydrator implements HydratorInterface {
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
        /** @var MaterialList $materialList */
        $materialList = $object;

        return [
            'id' => $materialList->getId(),
            'name' => $materialList->getName(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var MaterialList $materialList */
        $materialList = $object;

        if (isset($data['name'])) {
            $materialList->setName($data['name']);
        }

        return $materialList;
    }
}
