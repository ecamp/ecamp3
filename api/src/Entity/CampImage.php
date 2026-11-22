<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use App\State\CampImageDeliveryProcessor;
use App\State\CampImageProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new Get(
        uriTemplate: '/images/{sha256}/content',
        uriVariables: ['sha256' => new Link(fromClass: CampImage::class, identifiers: ['sha256'])],
        requirements: ['sha256' => '[0-9a-f]{64}'],
        outputFormats: ['avif' => ['image/avif']],
        openapi: new OpenApiOperation(
            summary: 'Read a camp image',
            responses: [
                '200' => new OpenApiResponse(
                    description: 'The processed image',
                    content: new \ArrayObject([
                        'image/avif' => ['schema' => ['type' => 'string', 'format' => 'binary']],
                    ]),
                ),
                '304' => new OpenApiResponse(description: 'The image has not changed'),
                '401' => new OpenApiResponse(description: 'Authentication required'),
                '403' => new OpenApiResponse(description: 'Access denied'),
                '404' => new OpenApiResponse(description: 'Image not found'),
            ],
        ),
        serialize: false,
        write: true,
        provider: CampImageProvider::class,
        processor: CampImageDeliveryProcessor::class,
        name: 'camp_image_download',
    ),
])]
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
