<?php

namespace App\Util;

use App\Entity\BaseEntity;

class EntityMap {
    private $map = [];

    public function add(BaseEntity $prototype, BaseEntity $entity) {
        $key = $prototype::class.'#'.$prototype->getId();
        $this->map[$key] = $entity;
    }

    public function get(BaseEntity $prototype): BaseEntity {
        $key = $prototype::class.'#'.$prototype->getId();
        $keyExists = array_key_exists($key, $this->map);

        return $keyExists ? $this->map[$key] : $prototype;
    }
}
