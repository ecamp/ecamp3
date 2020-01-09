<?php

namespace eCamp\Lib\Entity;

class EntityLink {
    public static function Create($entity) {
        if ($entity instanceof BaseEntity) {
            return new EntityLink($entity);
        }
        return null;
    }


    /** @var BaseEntity */
    private $entity;

    public $id;

    public function __construct(BaseEntity $entity) {
        $this->entity = $entity;
        $this->id = $entity->getId();
    }

    public function getEntity() {
        return $this->entity;
    }

    public function getId() {
        return $this->id;
    }
}
