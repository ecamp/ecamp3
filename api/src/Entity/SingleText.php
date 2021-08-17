<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_type_singletext")
 */
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class SingleText extends BaseContentTypeEntity {
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     */
    public ?string $text = null;

}
