<?php

namespace App\Entity;

interface SortableEntityInterface {
    public function getPosition(): int;

    public function setPosition(int $position): void;
}
