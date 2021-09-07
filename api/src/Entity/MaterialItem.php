<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\ContentNode\MaterialNode;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\AssertEitherIsNull;
use App\Validator\MaterialItemUpdateGroupSequence;
use Doctrine\ORM\Mapping as ORM;

/**
 * A physical item that is needed for carrying out a programme or camp.
 *
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'patch' => ['validation_groups' => MaterialItemUpdateGroupSequence::class],
        'delete',
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['materialList', 'period'])]
class MaterialItem extends BaseEntity implements BelongsToCampInterface {
    /**
     * The list to which this item belongs. Lists are used to keep track of who is
     * responsible to prepare and bring the item to the camp.
     *
     * @ORM\ManyToOne(targetEntity="MaterialList", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[AssertBelongsToSameCamp(compareToPrevious: true, groups: ['materialItem:update'])]
    #[ApiProperty(example: '/material_lists/1a2b3c4d')]
    public ?MaterialList $materialList = null;

    /**
     * The period to which this item belongs, if it does not belong to a content node.
     *
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherIsNull(other: 'materialNode')]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    public ?Period $period = null;

    /**
     * The content node to which this item belongs, if it does not belong to a period.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ContentNode\MaterialNode", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherisNull(other: 'period')]
    #[ApiProperty(example: '/content_node/material_nodes/1a2b3c4d')]
    public ?MaterialNode $materialNode = null;

    /**
     * The name of the item that is required.
     *
     * @ORM\Column(type="text", nullable=false)
     */
    #[ApiProperty(example: 'Volleyball')]
    public ?string $article = null;

    /**
     * The number of items or the amount in the unit of items that are required.
     *
     * @ORM\Column(type="float", nullable=true)
     */
    #[ApiProperty(example: 1.5)]
    public ?float $quantity = null;

    /**
     * An optional unit for measuring the amount of items required.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(example: 'kg')]
    public ?string $unit = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->materialList?->getCamp();
    }
}
