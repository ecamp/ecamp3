<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Defines a type of content that can be present in a content node tree. A content type
 * determines what data can be stored in content nodes of this type, as well as validation,
 * available slots and jsonConfig settings.
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ['groups' => ['read']],
)]
#[Entity]
class ContentType extends BaseEntity {
    /**
     * A name in UpperCamelCase of the content type. This value may be used as a technical
     * identifier of this content type, it is guaranteed to stay fixed.
     */
    #[ApiProperty(writable: false, example: 'SafetyConcept')]
    #[Groups(['read'])]
    #[Column(type: 'string', length: 32, unique: true)]
    public ?string $name = null;

    /**
     * Whether this content type is still maintained and recommended for use in new camps.
     */
    #[ApiProperty(writable: false, example: 'true')]
    #[Groups(['read'])]
    #[Column(type: 'boolean')]
    public bool $active = true;

    /**
     * The name of the internal PHP class that implements all custom behaviour of content nodes
     * of this type.
     */
    #[ApiProperty(writable: false)]
    #[Column(type: 'text')]
    public ?string $entityClass = null;

    /**
     * Internal configuration for the entityClass, in case the same entityClass is reused
     * for different content types.
     */
    #[ApiProperty(writable: false)]
    #[Column(type: 'json', nullable: true)]
    public ?array $jsonConfig = [];

    /**
     * API endpoint link for creating new entities of type entityClass.
     */
    #[Groups(['read'])]
    #[ApiProperty(
        example: '/content_node/column_layouts?contentType=%2Fcontent_types%2F1a2b3c4d',
        openapiContext: [
            'type' => 'array',
            'format' => 'iri-reference',
        ]
    )]
    public function getContentNodes(): array {
        return []; // empty here; actual content is filled/decorated in ContentTypeNormalizer
    }
}
