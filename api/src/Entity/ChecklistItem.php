<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode\ChecklistNode;
use App\InputFilter;
use App\Repository\ChecklistItemRepository;
use App\Util\EntityMap;
use App\Validator\AssertNoLoop;
use App\Validator\ChecklistItem\AssertBelongsToSameChecklist;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A ChecklistItem
 * A Checklist contains a Tree-Structure of ChecklistItems.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new Delete(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.checklist === null'
        ),
        new GetCollection(
            uriTemplate: self::CHECKLIST_SUBRESOURCE_URI_TEMPLATE,
            uriVariables: [
                'checklistId' => new Link(
                    fromClass: Checklist::class,
                    toProperty: 'checklist',
                    security: 'is_granted("CAMP_COLLABORATOR", checklist) or is_granted("CAMP_IS_PROTOTYPE", checklist)'
                ),
            ],
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['checklist.id', 'id'],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['checklist', 'checklist.camp'])]
#[ORM\Entity(repositoryClass: ChecklistItemRepository::class)]
#[ORM\UniqueConstraint(name: 'checklistitem_checklistid_parentid_position_unique', columns: ['checklistid', 'parentid', 'position'])]
class ChecklistItem extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface, HasParentInterface {
    public const CHECKLIST_SUBRESOURCE_URI_TEMPLATE = '/checklists/{checklistId}/checklist_items{._format}';

    /**
     * The Checklist this Item belongs to.
     */
    #[ApiProperty(example: '/checklists/1a2b3c4d')]
    #[Gedmo\SortableGroup]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Checklist::class, inversedBy: 'checklistItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Checklist $checklist = null;

    /**
     * The parent to which ChecklistItem item belongs. Is null in case this ChecklistItem is the
     * root of a ChecklistItem tree. For non-root ChecklistItems, the parent can be changed, as long
     * as the new parent is in the same checklist as the old one.
     *
     * Nesting has maxiaml 3 Levels (root - child - grandchild)
     * => CurrentNesting + SubtreeDepth < 3
     */
    #[AssertBelongsToSameChecklist]
    #[AssertNoLoop]
    #[Assert\Expression(
        '(this.getNestingLevel() + this.getSubtreeDepth()) < 3',
        'Nesting can be a maximum of 3 levels deep.'
    )]
    #[ApiProperty(example: '/checklist_items/1a2b3c4d')]
    #[Gedmo\SortableGroup]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: ChecklistItem::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    public ?ChecklistItem $parent = null;

    /**
     * All ChecklistItems that are direct children of this ChecklistItem.
     */
    #[ApiProperty(writable: false, example: '["/checklist_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: ChecklistItem::class, mappedBy: 'parent', cascade: ['persist'])]
    public Collection $children;

    /**
     * All ChecklistNodes that have selected this ChecklistItem.
     */
    #[ORM\ManyToMany(targetEntity: ChecklistNode::class, mappedBy: 'checklistItems')]
    public Collection $checklistNodes;

    /**
     * The human readable text of the checklist-item.
     */
    #[ApiProperty(example: 'Pfaditechnick')]
    #[Groups(['read', 'write'])]
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 256)]
    #[ORM\Column(type: 'text')]
    public ?string $text = null;

    /**
     * A whole number used for ordering multiple checklist items that are in the same parent.
     * The API does not guarantee the uniqueness of parent+position.
     */
    #[ApiProperty(example: '0')]
    #[Gedmo\SortablePosition]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $position = -1;

    public function __construct() {
        parent::__construct();
        $this->children = new ArrayCollection();
        $this->checklistNodes = new ArrayCollection();
    }

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->checklist?->getCamp();
    }

    public function getParent(): ?HasParentInterface {
        return $this->parent;
    }

    /**
     * @return ChecklistItem[]
     */
    public function getChildren(): array {
        return $this->children->getValues();
    }

    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->parent = $this;
        }

        return $this;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->parent === $this) {
                $child->parent = null;
            }
        }

        return $this;
    }

    /**
     * Nesting-Level of this ChecklistItem
     * Zero-Based (Parent == null  ->  NestingLevel == 0).
     */
    public function getNestingLevel(): int {
        $nesting = 0;
        $item = $this->parent;

        while (null !== $item && $nesting < 10 /* safetyguard */) {
            ++$nesting;
            $item = $item->parent;
        }

        return $nesting;
    }

    /**
     * Maximal SubtreeDepth.
     */
    public function getSubtreeDepth(): int {
        if (0 == $this->children->count()) {
            return 0;
        }

        return 1 + $this->children->reduce(function (int $max, ChecklistItem $child): int {
            $depth = $child->getSubtreeDepth();

            return ($depth > $max) ? $depth : $max;
        }, 0);
    }

    /**
     * @param ChecklistItem $prototype
     * @param EntityMap     $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        // copy ChecklistItem base properties
        $this->text = $prototype->text;

        // deep copy ChecklistItems
        foreach ($prototype->getChildren() as $childPrototype) {
            $child = new ChecklistItem();
            $this->addChild($child);
            $this->checklist->addChecklistItem($child);

            $child->copyFromPrototype($childPrototype, $entityMap);
        }
    }
}
