<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Repository\ColumnLayoutRepository;
use App\Validator\AssertJsonSchema;
use App\Validator\ColumnLayout\AssertColumWidthsSumTo12;
use App\Validator\ColumnLayout\AssertNoOrphanChildren;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_authenticated()',
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'create'],
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'update'],
        ],
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ORM\Entity(repositoryClass: ColumnLayoutRepository::class)]
class ColumnLayout extends ContentNode {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['columns'],
        'properties' => [
            'columns' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['slot', 'width'],
                    'properties' => [
                        'slot' => [
                            'type' => 'string',
                            'pattern' => '^[1-9][0-9]*$',
                        ],
                        'width' => [
                            'type' => 'integer',
                            'minimum' => 3,
                            'maximum' => 12,
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(example: ['columns' => [['slot' => '1', 'width' => 12]]])]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    #[Assert\Sequentially(constraints: [
        new AssertJsonSchema(schema: self::JSON_SCHEMA),
        new AssertColumWidthsSumTo12(),
        new AssertNoOrphanChildren(),
    ])]
    public ?array $data = ['columns' => [['slot' => '1', 'width' => 12]]];

    /**
     * All content nodes that are part of this content node tree.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: ContentNode::class, mappedBy: 'root')]
    public Collection $rootDescendants;

    public function __construct() {
        parent::__construct();
        $this->rootDescendants = new ArrayCollection();
    }

    /**
     * @return ContentNode[]
     */
    public function getRootDescendants(): array {
        return $this->rootDescendants->getValues();
    }

    public function addRootDescendant(ContentNode $rootDescendant): self {
        if (!$this->rootDescendants->contains($rootDescendant)) {
            $this->rootDescendants[] = $rootDescendant;
            $rootDescendant->root = $this;
        }

        return $this;
    }

    public function removeRootDescendant(ContentNode $rootDescendant): self {
        if ($this->rootDescendants->removeElement($rootDescendant)) {
            // reset the owning side (unless already changed)
            if ($rootDescendant->root === $this) {
                $rootDescendant->root = null;
            }
        }

        return $this;
    }
}
