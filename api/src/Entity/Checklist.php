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
use App\InputFilter;
use App\Repository\ChecklistRepository;
use App\State\ChecklistCreateProcessor;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Checklist
 * Tree-Structure with ChecklistItems.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CHECKLIST_IS_PROTOTYPE", object) or 
                       is_granted("CAMP_IS_PROTOTYPE", object) or 
                       (null != object.getCamp() and is_granted("CAMP_COLLABORATOR", object))
                      '
        ),
        new Patch(
            security: '(is_granted("CHECKLIST_IS_PROTOTYPE", object) and is_granted("ROLE_ADMIN")) or
                       (null != object.getCamp() and (is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)))
                      '
        ),
        new Delete(
            security: '(is_granted("CHECKLIST_IS_PROTOTYPE", object) and is_granted("ROLE_ADMIN")) or
                       (null != object.getCamp() and (is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)))
                      '
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: ChecklistCreateProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: '(is_granted("CHECKLIST_IS_PROTOTYPE", object) and is_granted("ROLE_ADMIN")) or
                                      (!is_granted("CHECKLIST_IS_PROTOTYPE", object) and (is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.camp === null))
                                     '
        ),
        new GetCollection(
            uriTemplate: self::CAMP_SUBRESOURCE_URI_TEMPLATE,
            uriVariables: [
                'campId' => new Link(
                    toProperty: 'camp',
                    fromClass: Camp::class,
                    security: 'is_granted("CAMP_COLLABORATOR", camp) or is_granted("CAMP_IS_PROTOTYPE", camp)'
                ),
            ],
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['camp.id', 'name'],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
class Checklist extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    public const CAMP_SUBRESOURCE_URI_TEMPLATE = '/camps/{campId}/checklists{._format}';

    /**
     * The camp this checklist belongs to.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[Assert\Expression('!(this.isPrototype == true and this.camp != null)', 'This value should be null.')]
    #[Assert\Expression('!(this.isPrototype == false and this.camp == null)', 'This value should not be null.')]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'checklists')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * Copy contents from this source checklist.
     */
    #[ApiProperty(example: '/checklists/1a2b3c4d')]
    #[Groups(['create'])]
    public ?Checklist $copyChecklistSource;

    /**
     * All ChecklistItems that belong to this Checklist.
     */
    #[ApiProperty(writable: false, uriTemplate: ChecklistItem::CHECKLIST_SUBRESOURCE_URI_TEMPLATE)]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: ChecklistItem::class, mappedBy: 'checklist', cascade: ['persist'])]
    public Collection $checklistItems;

    /**
     * The human readable name of the checklist.
     */
    #[ApiProperty(example: 'PBS Ausbildungsziele')]
    #[Groups(['read', 'write'])]
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(type: 'text')]
    public string $name;

    /**
     * Whether this checklist is a template.
     */
    #[Assert\Type('bool')]
    #[Assert\DisableAutoMapping]
    #[ApiProperty(example: true, writable: true)]
    #[Groups(['read', 'create'])]
    #[ORM\Column(type: 'boolean')]
    public bool $isPrototype = false;

    public function __construct() {
        parent::__construct();
        $this->checklistItems = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return ChecklistItem[]
     */
    public function getChecklistItems(): array {
        return $this->checklistItems->getValues();
    }

    public function addChecklistItem(ChecklistItem $checklistItem): self {
        if (!$this->checklistItems->contains($checklistItem)) {
            $this->checklistItems[] = $checklistItem;
            $checklistItem->checklist = $this;
        }

        return $this;
    }

    public function removeChecklistItem(ChecklistItem $checklistItem): self {
        if ($this->checklistItems->removeElement($checklistItem)) {
            // set the owning side to null (unless already changed)
            if ($checklistItem->checklist === $this) {
                $checklistItem->checklist = null;
            }
        }

        return $this;
    }

    /**
     * @param Checklist $prototype
     * @param EntityMap $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        // copy Checklist base properties
        $this->name = $prototype->name;

        // deep copy ChecklistItems
        foreach ($prototype->getChecklistItems() as $checklistItemPrototype) {
            // deep copy root ChecklistItems
            // skip non-root ChecklistItems as these are copyed by there parent
            if (null == $checklistItemPrototype->parent) {
                $checklistItem = new ChecklistItem();
                $this->addChecklistItem($checklistItem);

                $checklistItem->copyFromPrototype($checklistItemPrototype, $entityMap);
            }
        }
    }
}
