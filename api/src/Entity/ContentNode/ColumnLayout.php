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
use App\Repository\ColumnLayoutRepository;
use App\State\ContentNode\ContentNodePersistProcessor;
use App\Validator\AssertJsonSchema;
use App\Validator\ColumnLayout\AssertColumWidthsSumTo12;
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
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: ColumnLayoutRepository::class)]
class ColumnLayout extends ContentNode implements SupportsContentNodeChildren {
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
    #[Assert\NotNull]
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

    public function getSupportedSlotNames(): array {
        $columns = new ArrayCollection($this->getData()['columns']);

        return $columns->map(fn (array $element) => $element['slot'])->getValues();
    }
}
