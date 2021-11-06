<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait SortableEntityTrait {
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\SortablePosition
     */
    private int $pos = -1;

    #[Groups(['read'])]
    public function getPos(): int {
        return $this->pos;
    }

    #[Groups(['write'])]
    public function setPos(int $pos): void {
        $this->pos = $pos;
    }
}
