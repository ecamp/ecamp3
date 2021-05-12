<?php

namespace eCamp\ContentType\MultiSelect\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;
use eCamp\Lib\Entity\SortableEntityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_multiselect_option")
 */
class Option extends BaseContentTypeEntity implements SortableEntityInterface {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $pos;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $translateKey;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $checked = false;

    public function getPos(): int {
        return $this->pos;
    }

    /**
     * @param int $pos
     */
    public function setPos($pos): void {
        $this->pos = $pos;
    }

    public function getTranslateKey(): string {
        return $this->translateKey;
    }

    /**
     * @param string $translateKey
     */
    public function setTranslateKey($translateKey): void {
        $this->translateKey = $translateKey;
    }

    public function getChecked(): bool {
        return $this->checked;
    }

    /**
     * @param bool $checked
     */
    public function setChecked($checked): void {
        $this->checked = $checked;
    }
}
