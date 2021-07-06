<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
#[ApiResource]
class ContentType extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    public ?string $name = null;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $active = true;

    /**
     * @ORM\Column(type="text")
     */
    public ?string $strategyClass = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    public ?array $jsonConfig = [];
}
