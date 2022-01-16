<?php

namespace App\Entity;

use App\Util\EntityMap;

class CopyFromPrototype {
    /**
     * @param BaseEntity $entity
     * @param BaseEntity $prototype
     * @param EntityMap  $entityMap
     */
    public static function add($entity, $prototype, &$entityMap = null) {
        $entityMap ??= new EntityMap();
        $entityMap->add($prototype, $entity);
    }
}
