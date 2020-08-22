<?php

namespace eCamp\ContentType\SingleText\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\ContentType\BaseContentTypeEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_singletext")
 */
class SingleText extends BaseContentTypeEntity {
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
