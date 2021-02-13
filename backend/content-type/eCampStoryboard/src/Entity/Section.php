<?php

namespace eCamp\ContentType\Storyboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_storyboard_section")
 */
class Section extends BaseContentTypeEntity {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $pos = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $column1 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $column2 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string  $column3 = null;

    public function getPos(): int {
        return $this->pos;
    }

    public function setPos(int $pos): void {
        $this->pos = $pos;
    }

    public function getColumn1(): ?string {
        return $this->column1;
    }

    public function setColumn1(?string $text) {
        $this->column1 = $text;
    }

    public function getColumn2(): ?string {
        return $this->column2;
    }

    public function setColumn2(?string $text) {
        $this->column2 = $text;
    }

    public function getColumn3(): ?string {
        return $this->column3;
    }

    public function setColumn3(?string $text) {
        $this->column3 = $text;
    }
}
