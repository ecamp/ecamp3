<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\ContentNode\MaterialNode;
use App\Repository\MaterialItemRepository;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\AssertEitherIsNull;
use App\Validator\MaterialItemUpdateGroupSequence;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A physical item that is needed for carrying out a programme or camp.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => ['security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'validation_groups' => MaterialItemUpdateGroupSequence::class,
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['materialList', 'period'])]
#[Entity(repositoryClass: MaterialItemRepository::class)]
class MaterialItem extends BaseEntity implements BelongsToCampInterface {
    /**
     * The list to which this item belongs. Lists are used to keep track of who is
     * responsible to prepare and bring the item to the camp.
     */
    #[AssertBelongsToSameCamp(compareToPrevious: true, groups: ['update'])]
    #[ApiProperty(example: '/material_lists/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ManyToOne(targetEntity: 'MaterialList', inversedBy: 'materialItems')]
    #[JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?MaterialList $materialList = null;

    /**
     * The period to which this item belongs, if it does not belong to a content node.
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherIsNull(other: 'materialNode')]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ManyToOne(targetEntity: 'Period', inversedBy: 'materialItems')]
    #[JoinColumn(nullable: true, onDelete: 'cascade')]
    public ?Period $period = null;

    /**
     * The content node to which this item belongs, if it does not belong to a period.
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherisNull(other: 'period')]
    #[ApiProperty(example: '/content_node/material_nodes/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ManyToOne(targetEntity: 'App\Entity\ContentNode\MaterialNode', inversedBy: 'materialItems')]
    #[JoinColumn(nullable: true, onDelete: 'CASCADE')]
    public ?MaterialNode $materialNode = null;

    /**
     * The name of the item that is required.
     */
    #[ApiProperty(example: 'Volleyball')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: false)]
    public ?string $article = null;

    /**
     * The number of items or the amount in the unit of items that are required.
     */
    #[ApiProperty(example: 1.5)]
    #[Groups(['read', 'write'])]
    #[Column(type: 'float', nullable: true)]
    public ?float $quantity = null;

    /**
     * An optional unit for measuring the amount of items required.
     */
    #[ApiProperty(example: 'kg')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $unit = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->materialList?->getCamp();
    }
}
