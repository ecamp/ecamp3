<?php

namespace eCamp\ContentType\MultiSelect\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_multiselect_option")
 */
class Option extends BaseContentTypeEntity {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $pos;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $key;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $checked = false;

    /**
     * @return int
     */
    public function getPos() {
        return $this->pos;
    }

    /**
     * @param int $pos
     */
    public function setPos($pos): void {
        $this->pos = $pos;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key): void {
        $this->key = $key;
    }

    /**
     * @return bool
     */
    public function getChecked() {
        return $this->checked;
    }

    /**
     * @param bool $checked
     */
    public function setChecked($checked): void {
        $this->checked = $checked;
    }
}
