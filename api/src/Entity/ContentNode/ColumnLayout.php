<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Repository\ColumnLayoutRepository;
use App\Validator\AssertJsonSchema;
use App\Validator\ColumnLayout\AssertColumWidthsSumTo12;
use App\Validator\ColumnLayout\AssertNoOrphanChildren;
use App\Validator\ColumnLayout\ColumnLayoutPatchGroupSequence;
use App\Validator\ColumnLayout\ColumnLayoutPostGroupSequence;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_fully_authenticated()',
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ColumnLayoutPostGroupSequence::class,
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ColumnLayoutPatchGroupSequence::class,
        ],
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.owner === null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[Entity(repositoryClass: ColumnLayoutRepository::class)]
#[Table(name: 'content_node_columnlayout')]
class ColumnLayout extends ContentNode {
    public const COLUMNS_SCHEMA = [
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
    ];

    /**
     * JSON configuration for columns.
     */
    #[ApiProperty(example: "[['slot' => '1', 'width' => 12]]")]
    #[Groups(['read', 'write'])]
    #[Column(type: 'json', nullable: true)]
    private ?array $columns = null;

    #[AssertJsonSchema(schema: ColumnLayout::COLUMNS_SCHEMA, groups: ['columns_schema'])]
    #[AssertColumWidthsSumTo12]
    #[AssertNoOrphanChildren]
    public function getColumns(): ?array {
        if (null !== $this->prototype && null === $this->columns) {
            return $this->prototype->columns;
        }

        return $this->columns;
    }

    public function setColumns(?array $columns) {
        $this->columns = $columns;
    }

    public function copyFromPrototype(ColumnLayout $prototype) {
        if (!isset($this->columns)) {
            $this->columns = $prototype->getColumns();
        }
    }
}
