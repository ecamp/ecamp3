<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Filter\MaterialItemPeriodFilter;
use App\Entity\ContentNode\MaterialNode;
use App\InputFilter;
use App\Repository\MaterialItemRepository;
use App\State\MaterialItemCollectionProvider;
use App\State\MaterialItemCreateProcessor;
use App\Util\EntityMap;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\AssertEitherIsNull;
use App\Validator\MaterialItemUpdateGroupSequence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A physical item that is needed for carrying out a programme or camp.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or
                       is_granted("CAMP_IS_PUBLIC", object)'
        ),
        new Patch(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => MaterialItemUpdateGroupSequence::class]
        ),
        new Delete(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            security: 'is_authenticated()',
            provider: MaterialItemCollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or (object.period === null and object.materialNode === null)',
            processor: MaterialItemCreateProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp', 'materialList', 'materialNode'])]
#[ApiFilter(filterClass: MaterialItemPeriodFilter::class)]
#[ORM\Entity(repositoryClass: MaterialItemRepository::class)]
class MaterialItem extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    /**
     * The Camp to which this item belongs.
     * Either Period or MaterialNode is always set. This reference is
     * therefore redundant - but ensures significantly better performance.
     */
    #[Assert\DisableAutoMapping] // camp is set in the data processor
    #[ApiProperty(writable: false, example: '/camps/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'materialItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The list to which this item belongs. Lists are used to keep track of who is
     * responsible to prepare and bring the item to the camp.
     */
    #[Assert\NotNull]
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/material_lists/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: MaterialList::class, inversedBy: 'materialItems')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'cascade')]
    public ?MaterialList $materialList = null;

    /**
     * The period to which this item belongs, if it does not belong to a content node.
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherIsNull(other: 'materialNode')]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: Period::class, inversedBy: 'materialItems')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'cascade')]
    public ?Period $period = null;

    /**
     * List of PeriodMaterailItems
     * one entry for each affected Period.
     */
    #[ORM\OneToMany(targetEntity: PeriodMaterialItem::class, mappedBy: 'materialItem')]
    public Collection $periodMaterialItems;

    /**
     * The content node to which this item belongs, if it does not belong to a period.
     */
    #[AssertBelongsToSameCamp]
    #[AssertEitherIsNull(other: 'period')]
    #[ApiProperty(example: '/content_node/material_nodes/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: MaterialNode::class, inversedBy: 'materialItems')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    public ?MaterialNode $materialNode = null;

    /**
     * The name of the item that is required.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ApiProperty(example: 'Volleyball')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: false)]
    public ?string $article = null;

    /**
     * The number of items or the amount in the unit of items that are required.
     */
    #[ApiProperty(example: 1.5)]
    #[Assert\GreaterThan(0)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'float', nullable: true)]
    public ?float $quantity = null;

    /**
     * An optional unit for measuring the amount of items required.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'kg')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $unit = null;

    /**
     * Whether the item has been prepared and is ready to be brought to the camp.
     */
    #[ApiProperty(example: true)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $done = false;

    public function __construct() {
        parent::__construct();
        $this->periodMaterialItems = new ArrayCollection();
    }

    #[\Override]
    public function getCamp(): ?Camp {
        return $this->camp ?? $this->period->camp ?? $this->materialNode?->getCamp();
    }

    /**
     * @param MaterialItem $prototype
     * @param EntityMap    $entityMap
     */
    #[\Override]
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        if (null != $prototype->materialList) {
            /** @var MaterialList $materialList */
            $materialList = $entityMap->get($prototype->materialList);

            if ($entityMap->belongsToTargetCamp($materialList)) {
                $materialList->addMaterialItem($this);
            }
        }

        $this->article = $prototype->article;
        $this->quantity = $prototype->quantity;
        $this->unit = $prototype->unit;

        $entityMap->getTargetCamp()->addMaterialItem($this);
    }
}
