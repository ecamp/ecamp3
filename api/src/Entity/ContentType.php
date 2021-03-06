<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * Defines a type of content that can be present in a content node tree. A content type
 * determines what data can be stored in content nodes of this type, as well as validation,
 * available slots and jsonConfig settings.
 *
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
)]
class ContentType extends BaseEntity {
    /**
     * A name in UpperCamelCase of the content type. This value may be used as a technical
     * identifier of this content type, it is guaranteed to stay fixed.
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    #[ApiProperty(writable: false, example: 'SafetyConcept')]
    public ?string $name = null;

    /**
     * Whether this content type is still maintained and recommended for use in new camps.
     *
     * @ORM\Column(type="boolean")
     */
    #[ApiProperty(writable: false, example: 'true')]
    public bool $active = true;

    /**
     * The name of the internal PHP class that implements all custom behaviour of content nodes
     * of this type.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?string $strategyClass = null;

    /**
     * Internal configuration for the strategyClass, in case the same strategyClass is reused
     * for different content types.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?array $jsonConfig = [];
}
