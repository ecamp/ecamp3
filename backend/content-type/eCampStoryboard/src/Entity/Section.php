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
    private $text;

    /** @return int */
    public function getPos() {
        return $this->pos;
    }

    /** @param int $pos */
    public function setPos($pos): void {
        $this->pos = $pos;
    }

    /** @return string */
    public function getText() {
        return $this->text;
    }

    /** @param string $text */
    public function setText($text) {
        $this->text = $text;
    }
}
