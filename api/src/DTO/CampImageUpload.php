<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\State\CampImageUploadProcessor;
use App\State\CampImageUploadProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/camps/{campId}/images',
            uriVariables: ['campId' => new Link(fromClass: Camp::class, identifiers: ['id'])],
            status: 201,
            inputFormats: ['multipart' => ['multipart/form-data']],
            outputFormats: ['json' => ['application/json']],
            openapi: new Operation(
                summary: 'Upload an image to a camp',
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['file'],
                                'properties' => ['file' => ['type' => 'string', 'format' => 'binary']],
                            ],
                        ],
                    ]),
                    required: true,
                ),
            ),
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            read: true,
            deserialize: false,
            validate: false,
            provider: CampImageUploadProvider::class,
            processor: CampImageUploadProcessor::class,
            name: 'camp_image_upload',
        ),
    ],
    normalizationContext: ['groups' => ['read']],
)]
class CampImageUpload implements BelongsToCampInterface {
    #[ApiProperty(readable: false, writable: false)]
    public ?Camp $camp = null;

    /**
     * The identifier of the image within the camp.
     */
    #[ApiProperty(identifier: true, writable: false)]
    #[Groups(['read'])]
    public ?string $id = null;

    /**
     * The camp image URL.
     */
    #[Groups(['read'])]
    public string $src;

    /**
     * The media type of the processed image.
     */
    #[Groups(['read'])]
    public string $mediaType;

    /**
     * The processed file size in bytes.
     */
    #[Groups(['read'])]
    public int $byteSize;

    /**
     * The processed image width in pixels.
     */
    #[Groups(['read'])]
    public int $width;

    /**
     * The processed image height in pixels.
     */
    #[Groups(['read'])]
    public int $height;

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
