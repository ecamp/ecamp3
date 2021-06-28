<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ApiResource]
class Organization extends BaseEntity {
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public ?string $name = null;
}
