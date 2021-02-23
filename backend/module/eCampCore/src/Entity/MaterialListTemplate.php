<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialListTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="CampTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?CampTemplate $campTemplate = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    public function getCampTemplate(): ?CampTemplate {
        return $this->campTemplate;
    }

    public function setCampTemplate(?CampTemplate $campTemplate): void {
        $this->campTemplate = $campTemplate;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }
}
