<?php

namespace eCamp\Lib\Entity;

interface SortableEntityInterface {
    public function getPos(): int;

    public function setPos(int $pos): void;
}
