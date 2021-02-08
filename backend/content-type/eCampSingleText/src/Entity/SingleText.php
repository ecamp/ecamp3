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
    private ?string $text = null;

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $text): void {
        $this->text = $text;
    }
}
