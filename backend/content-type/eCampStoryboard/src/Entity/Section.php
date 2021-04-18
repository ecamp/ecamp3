<?php

namespace eCamp\ContentType\Storyboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;
use eCamp\Lib\Entity\SortableEntityInterface;
use eCamp\Lib\Entity\SortableEntityTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_storyboard_section")
 */
class Section extends BaseContentTypeEntity implements SortableEntityInterface {
    use SortableEntityTrait;

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

    public function getColumn1(): ?string {
        return $this->column1;
    }

    public function setColumn1(?string $text): void {
        $this->column1 = $text;
    }

    public function getColumn2(): ?string {
        return $this->column2;
    }

    public function setColumn2(?string $text): void {
        $this->column2 = $text;
    }

    public function getColumn3(): ?string {
        return $this->column3;
    }

    public function setColumn3(?string $text): void {
        $this->column3 = $text;
    }
}
