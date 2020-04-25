<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugins")
 */
class Plugin extends BaseEntity {
    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $strategyClass;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function getActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getStrategyClass(): string {
        return $this->strategyClass;
    }

    public function setStrategyClass(string $strategyClass): void {
        $this->strategyClass = $strategyClass;
    }
}
