<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['categories'])]
class ContentType extends BaseEntity {
    /**
     * A name in UpperCamelCase of the content type. This value may be used as a technical
     * identifier of this content type, it is guaranteed to stay fixed.
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    #[ApiProperty(writable: false, example: 'SafetyConcept')]
    #[Groups(['read'])]
    public ?string $name = null;

    /**
     * Whether this content type is still maintained and recommended for use in new camps.
     *
     * @ORM\Column(type="boolean")
     */
    #[ApiProperty(writable: false, example: 'true')]
    #[Groups(['read'])]
    public bool $active = true;

    /**
     * The name of the internal PHP class that implements all custom behaviour of content nodes
     * of this type.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(writable: false)]
    public ?string $entityClass = null;

    /**
     * Internal configuration for the entityClass, in case the same entityClass is reused
     * for different content types.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    #[ApiProperty(writable: false)]
    public ?array $jsonConfig = [];

    /**
     * Backlink to category (only used for filtering contentTypes by category).
     * Internal: not published via API.
     *
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="preferredContentTypes")
     */
    public Collection $categories;

    public function __construct() {
        $this->categories = new ArrayCollection();
    }

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
