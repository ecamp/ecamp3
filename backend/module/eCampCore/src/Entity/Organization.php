<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class Organization extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }
}
