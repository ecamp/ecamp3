<?php

namespace eCamp\Plugin\Textarea\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Plugin\BasePluginEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugin_textarea_textareas")
 */
class Textarea extends BasePluginEntity
{

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;


    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }
}
