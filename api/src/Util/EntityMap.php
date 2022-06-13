<?php

namespace App\Util;

use ApiPlatform\Core\Util\ClassInfoTrait;
use App\Entity\BaseEntity;

class EntityMap {
    use ClassInfoTrait;

    private $map = [];

    public function add(BaseEntity $prototype, BaseEntity $entity) {
        $key = $this->getObjectClass($prototype).'#'.$prototype->getId();
        $this->map[$key] = $entity;
    }

    public function get(BaseEntity $prototype): BaseEntity {
        $key = $this->getObjectClass($prototype).'#'.$prototype->getId();
        $keyExists = array_key_exists($key, $this->map);

        return $keyExists ? $this->map[$key] : $prototype;
    }
}
