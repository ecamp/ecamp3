<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use App\Entity\ContentNode;
use App\Repository\ChecklistNodeRepository;
use App\State\ContentNode\ChecklistNodePersistProcessor;
use App\Util\EntityMap;
use App\Validator\ChecklistItem\AssertBelongsToSameCamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            processor: ChecklistNodePersistProcessor::class,
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
            processor: ChecklistNodePersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.parent === null',
            validationContext: ['groups' => ['Default', 'create']],
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: ChecklistNodeRepository::class)]
class ChecklistNode extends ContentNode {
    /**
     * List of selected ChecklistItems.
     */
    #[ApiProperty(example: '["/checklist_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\ManyToMany(targetEntity: ChecklistItem::class, inversedBy: 'checklistNodes')]
    #[ORM\JoinTable(name: 'checklistnode_checklistitem')]
    #[ORM\JoinColumn(name: 'checklistnode_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'checklistitem_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    public Collection $checklistItems;

    #[AssertBelongsToSameCamp(groups: ['update'])]
    #[ApiProperty(example: '["1a2b3c4d"]')]
    #[Groups(['write'])]
    public ?array $addChecklistItemIds = [];

    #[ApiProperty(example: '["1a2b3c4d"]')]
    #[Groups(['write'])]
    public ?array $removeChecklistItemIds = [];

    public function __construct() {
        parent::__construct();
        $this->checklistItems = new ArrayCollection();
    }

    /**
     * @return ChecklistItem[]
     */
    public function getChecklistItems(): array {
        return $this->checklistItems->getValues();
    }

    public function addChecklistItem(ChecklistItem $checklistItem) {
        $this->checklistItems->add($checklistItem);
    }

    public function removeChecklistItem(ChecklistItem $checklistItem) {
        $this->checklistItems->removeElement($checklistItem);
    }

    /**
     * @param ChecklistNode $prototype
     * @param EntityMap     $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        parent::copyFromPrototype($prototype, $entityMap);

        // copy all checklist-items
        foreach ($prototype->checklistItems as $itemPrototype) {
            /** @var ChecklistItem $itemPrototype */
            /** @var ChecklistItem $checklilstItem */
            $checklilstItem = $entityMap->get($itemPrototype);
            $this->addChecklistItem($checklilstItem);
        }
    }
}
