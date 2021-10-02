<?php

namespace App\Entity;

interface SortableEntityInterface {
    public function getPos(): int;

    public function setPos(int $pos): void;
}
