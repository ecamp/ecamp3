<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class ContentType extends BaseEntity {
    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $strategyClass;

    public function __construct() {
        parent::__construct();
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function getStrategyClass(): string {
        return $this->strategyClass;
    }

    public function setStrategyClass(string $strategyClass): void {
        $this->strategyClass = $strategyClass;
    }
}
