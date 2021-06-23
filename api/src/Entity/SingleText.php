<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SingleTextRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SingleTextRepository::class)
 * @ORM\Table(name="content_type_singletext")
 */
#[ApiResource]
class SingleText extends BaseEntity {
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $text = null;
}
