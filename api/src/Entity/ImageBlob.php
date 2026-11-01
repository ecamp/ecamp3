<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'image_blob_sha256_unique', columns: ['sha256'])]
class ImageBlob extends BaseEntity {
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/\A[0-9a-f]{64}\z/')]
    #[ORM\Column(type: 'string', length: 64)]
    public string $sha256;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/\Aimage\/[a-zA-Z0-9.+-]+\z/')]
    #[ORM\Column(type: 'string', length: 255)]
    public string $mediaType;

    #[Assert\Positive]
    #[ORM\Column(type: 'integer')]
    public int $byteSize;

    #[Assert\Positive]
    #[ORM\Column(type: 'integer')]
    public int $width;

    #[Assert\Positive]
    #[ORM\Column(type: 'integer')]
    public int $height;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $unreferencedSince = null;
}
