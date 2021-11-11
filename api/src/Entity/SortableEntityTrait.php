<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Serializer\Annotation\Groups;

trait SortableEntityTrait {
    #[Column(type: 'integer', nullable: false)]
    private int $pos = 0;

    #[Groups(['read'])]
    public function getPos(): int {
        return $this->pos;
    }

    #[Groups(['write'])]
    public function setPos(int $pos): void {
        $this->pos = $pos;
    }
}
