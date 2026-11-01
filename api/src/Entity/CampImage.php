<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'camp_image_camp_blob_unique', columns: ['campId', 'imageBlobId'])]
class CampImage extends BaseEntity implements BelongsToCampInterface {
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Camp::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Camp $camp = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: ImageBlob::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?ImageBlob $imageBlob = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $unreferencedSince = null;

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
