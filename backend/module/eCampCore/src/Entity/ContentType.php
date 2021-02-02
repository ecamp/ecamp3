<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;
use Laminas\Json\Json;

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

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $jsonConfig = [];

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

    public function getJsonConfig(): array {
        return $this->jsonConfig;
    }

    public function setJsonConfig(array $jsonConfig): void {
        $this->jsonConfig = $jsonConfig;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key = null) {
        if (null != $this->jsonConfig) {
            if (null != $key) {
                return $this->jsonConfig[$key];
            }
        }

        return $this->jsonConfig;
    }
}
