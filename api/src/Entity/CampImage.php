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
        uriTemplate: '/camps/{campId}/images/{id}',
        uriVariables: [
            'campId' => new Link(fromClass: Camp::class, identifiers: ['id']),
            'id' => new Link(fromClass: CampImage::class, identifiers: ['id']),
        ],
        outputFormats: ['webp' => ['image/webp']],
        openapi: new OpenApiOperation(
            summary: 'Read a camp image',
            responses: [
                '200' => new OpenApiResponse(
                    description: 'The processed image',
                    content: new \ArrayObject(['image/webp' => ['schema' => ['type' => 'string', 'format' => 'binary']]]),
                ),
                '304' => new OpenApiResponse(description: 'The image has not changed'),
                '401' => new OpenApiResponse(description: 'Authentication required'),
                '403' => new OpenApiResponse(description: 'Access denied'),
                '404' => new OpenApiResponse(description: 'Image not found in this camp'),
            ],
        ),
        security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PUBLIC", object)',
        serialize: false,
        write: true,
        provider: CampImageProvider::class,
        processor: CampImageDeliveryProcessor::class,
        name: 'camp_image_download',
    ),
])]
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
