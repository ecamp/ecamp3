<?php

namespace eCamp\Lib\Entity;

class EntityLink {

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
