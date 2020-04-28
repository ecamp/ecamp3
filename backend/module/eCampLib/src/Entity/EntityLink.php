<?php

namespace eCamp\Lib\Entity;

class EntityLink {
    public $id;

    /** @var BaseEntity */
    private $entity;

    public function __construct(BaseEntity $entity) {
        $this->entity = $entity;
        $this->id = $entity->getId();
    }

    public static function Create($entity) {
        if ($entity instanceof BaseEntity) {
            return new EntityLink($entity);
        }

        return null;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function getId() {
        return $this->id;
    }
}
