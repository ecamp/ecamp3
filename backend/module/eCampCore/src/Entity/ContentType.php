<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentType extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $active = true;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private ?string $strategyClass = null;

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function getStrategyClass(): ?string {
        return $this->strategyClass;
    }

    public function setStrategyClass(?string $strategyClass): void {
        $this->strategyClass = $strategyClass;
    }
}
