<?php

namespace eCamp\Lib\Entity;

class EntityLink {
    public string $id;

    private BaseEntity $entity;

    public function __construct(BaseEntity $entity) {
        $this->entity = $entity;
        $this->id = $entity->getId();
    }

    public static function Create($entity): ?EntityLink {
        if ($entity instanceof BaseEntity) {
            return new EntityLink($entity);
        }

        return null;
    }

    public function getEntity(): BaseEntity {
        return $this->entity;
    }

    public function getId(): string {
        return $this->id;
    }
}
