<?php

namespace eCamp\Plugin\Storyboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Plugin\BasePluginEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugin_storyboard_sections")
 */
class Section extends BasePluginEntity
{
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
