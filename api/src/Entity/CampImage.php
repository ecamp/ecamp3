<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Index(name: 'camp_image_sha256_idx', columns: ['sha256'])]
#[ORM\UniqueConstraint(name: 'camp_image_camp_sha256_unique', columns: ['campId', 'sha256'])]
class CampImage extends BaseEntity implements BelongsToCampInterface {
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Camp::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Camp $camp = null;

    /** SHA-256 hash of the processed image bytes. */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/\A[0-9a-f]{64}\z/')]
    #[ORM\Column(type: 'string', length: 64)]
    public string $sha256;

    /** Media type of the stored image. */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/\Aimage\/[a-zA-Z0-9.+-]+\z/')]
    #[ORM\Column(type: 'string', length: 255)]
    public string $mediaType;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $unreferencedSince = null;

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
