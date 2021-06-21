<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContentTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentTypeRepository::class)
 */
#[ApiResource]
class ContentType extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = true;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $strategyClass = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $jsonConfig = [];

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getActive(): ?bool {
        return $this->active;
    }

    public function setActive(bool $active): self {
        $this->active = $active;

        return $this;
    }

    public function getStrategyClass(): ?string {
        return $this->strategyClass;
    }

    public function setStrategyClass(string $strategyClass): self {
        $this->strategyClass = $strategyClass;

        return $this;
    }

    public function getJsonConfig(): ?array {
        return $this->jsonConfig;
    }

    public function setJsonConfig(?array $jsonConfig): self {
        $this->jsonConfig = $jsonConfig;

        return $this;
    }
}
