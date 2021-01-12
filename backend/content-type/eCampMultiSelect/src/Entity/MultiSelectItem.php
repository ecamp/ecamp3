<?php

namespace eCamp\ContentType\MultiSelect\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_multiselect_item")
 */
class MultiSelectItem extends BaseContentTypeEntity {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $pos;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $description;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $translated;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $checked;

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
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function getTranslated() {
        return $this->translated;
    }

    /**
     * @param bool $translated
     */
    public function setTranslated($translated): void {
        $this->translated = $translated;
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
