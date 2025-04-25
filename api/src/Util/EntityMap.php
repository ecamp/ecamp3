<?php

namespace App\Util;

use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;

class EntityMap {
    use ClassInfoTrait;

    private $map = [];

    public function __construct(private Camp $targetCamp) {}

    public function add(BaseEntity $prototype, BaseEntity $entity) {
        $key = $this->getObjectClass($prototype).'#'.$prototype->getId();
        $this->map[$key] = $entity;
    }

    public function get(BaseEntity $prototype): BaseEntity {
        $key = $this->getObjectClass($prototype).'#'.$prototype->getId();
        $keyExists = array_key_exists($key, $this->map);

        return $keyExists ? $this->map[$key] : $prototype;
    }

    public function belongsToTargetCamp(BelongsToCampInterface $entity) {
        return $entity->getCamp() == $this->targetCamp;
    }

    public function getTargetCamp(): Camp {
        return $this->targetCamp;
    }
}
