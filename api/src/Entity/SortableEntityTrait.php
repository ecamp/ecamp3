<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait SortableEntityTrait {
    /**
     * Property to sort items within the same sorting group. First entry starts with 0. Choose -1 to place item at the end of the list (e.g. for new items).
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\SortablePosition
     */
    private int $position = -1;

    #[Groups(['read'])]
    public function getPosition(): int {
        return $this->position;
    }

    #[Groups(['write'])]
    public function setPosition(int $position): void {
        $this->position = $position;
    }
}
