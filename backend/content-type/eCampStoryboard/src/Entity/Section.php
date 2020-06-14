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
    private $pos;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $column1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $column2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $column3;

    /** @return int */
    public function getPos() {
        return $this->pos;
    }

    /** @param int $pos */
    public function setPos($pos): void {
        $this->pos = $pos;
    }

    /** @return string */
    public function getColumn1() {
        return $this->column1;
    }

    /** @param string $text */
    public function setColumn1($text) {
        $this->column1 = $text;
    }

    /** @return string */
    public function getColumn2() {
        return $this->column2;
    }

    /** @param string $text */
    public function setColumn2($text) {
        $this->column2 = $text;
    }

    /** @return string */
    public function getColumn3() {
        return $this->column3;
    }

    /** @param string $text */
    public function setColumn3($text) {
        $this->column3 = $text;
    }
}
