<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="medium")
 */
class Medium {
    const MEDIUM_WEB = 'web';
    const MEDIUM_PRINT = 'print';
    const MEDIUM_MOBILE = 'mobile';


    public function __construct() {
    }

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, name="isdefault")
     */
    private $default;


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
    public function isDefault(): bool {
        return $this->default;
    }

    public function setDefault(bool $default): void {
        $this->default = $default;
    }
}
