<?php

namespace App\Entity;

trait SortableEntityTrait {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $pos = 0;

    public function getPos(): int {
        return $this->pos;
    }

    public function setPos(int $pos): void {
        $this->pos = $pos;
    }
}
