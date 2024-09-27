<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode;
use App\Entity\SupportsContentNodeChildren;
use App\Repository\ResponsiveLayoutRepository;
use App\State\ContentNode\ContentNodePersistProcessor;
use App\Validator\AssertJsonSchema;
use App\Validator\ColumnLayout\AssertNoOrphanChildren;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            processor: ContentNodePersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']]
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: ContentNodePersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.parent === null',
            validationContext: ['groups' => ['Default', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: ResponsiveLayoutRepository::class)]
class ResponsiveLayout extends ContentNode implements SupportsContentNodeChildren {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['items'],
        'properties' => [
            'items' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['slot'],
                    'properties' => [
                        'slot' => [
                            'type' => 'string',
                            'enum' => ['main', 'aside-top', 'aside-bottom'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public const DATA_DEFAULT = '{"items":[{"slot":"main"},{"slot":"aside-top"},{"slot":"aside-bottom"}]}';

    /**
     * All content nodes that are part of this content node tree.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: ContentNode::class, mappedBy: 'root')]
    public Collection $rootDescendants;

    public function __construct() {
        parent::__construct();
        $this->rootDescendants = new ArrayCollection();
        $this->data = json_decode(self::DATA_DEFAULT, true);
    }

    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(
        default: self::DATA_DEFAULT,
        example: ['items' => [['slot' => 'main'], ['slot' => 'aside-top'], ['slot' => 'aside-bottom']]],
    )]
    #[Groups(['read', 'write'])]
    #[Assert\Sequentially(constraints: [
        new AssertJsonSchema(schema: self::JSON_SCHEMA),
        new AssertNoOrphanChildren(),
    ])]
    #[Assert\NotNull]
    public function getData(): ?array {
        return parent::getData();
    }

    public function getSupportedSlotNames(): array {
        $items = new ArrayCollection($this->getData()['items']);

        return $items->map(fn (array $element) => $element['slot'])->getValues();
    }
}
