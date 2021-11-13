<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

trait SortableEntityTrait {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
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
