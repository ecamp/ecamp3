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
use App\State\ContentNodeCollectionProvider;
use App\Util\EntityMap;
use App\Validator\ChecklistItem\AssertBelongsToSameCamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or
                       is_granted("CAMP_IS_PUBLIC", object)'
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']],
            processor: ChecklistNodePersistProcessor::class
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()',
            provider: ContentNodeCollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.parent === null',
            validationContext: ['groups' => ['Default', 'create']],
            processor: ChecklistNodePersistProcessor::class,
        ),
    ],
    routePrefix: '/content_node',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\Entity(repositoryClass: ChecklistNodeRepository::class)]
class ChecklistNode extends ContentNode {
    /**
     * List of selected ChecklistItems.
     */
    #[ApiProperty(
        example: '["/checklist_items/1a2b3c4d"]',
        uriTemplate: ChecklistItem::CHECKLISTNODE_SUBRESOURCE_URI_TEMPLATE
    )]
    #[Groups(['read'])]
    #[ORM\ManyToMany(targetEntity: ChecklistItem::class, inversedBy: 'checklistNodes')]
    #[ORM\JoinTable(name: 'checklistnode_checklistitem')]
    #[ORM\JoinColumn(name: 'checklistnode_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'checklistitem_id', referencedColumnName: 'id', onDelete: 'RESTRICT')]
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
    #[\Override]
    public function copyFromPrototype($prototype, $entityMap): void {
        parent::copyFromPrototype($prototype, $entityMap);

        // copy the connections to checklist items
        if ($entityMap->belongsToTargetCamp($prototype)) {
            foreach ($prototype->checklistItems as $itemPrototype) {
                /** @var ChecklistItem $itemPrototype */
                /** @var ChecklistItem $checklistItem */
                $checklistItem = $entityMap->get($itemPrototype);
                $this->addChecklistItem($checklistItem);
            }
        } else {
            /** @var ChecklistItem[] $checklistItemsInCamp */
            $checklistItemsInCamp = array_merge(...array_values(array_map(function (Checklist $checklist) {
                return $checklist->getChecklistItems();
            }, $entityMap->getTargetCamp()->getChecklists())));
            foreach ($prototype->checklistItems as $itemPrototype) {
                /** @var ChecklistItem $itemPrototype */
                /** @var ChecklistItem $knownEquivalent */
                // First, look up whether we already know a replacement
                $knownEquivalent = $entityMap->get($itemPrototype);
                if ($knownEquivalent !== $itemPrototype) {
                    $this->addChecklistItem($knownEquivalent);

                    continue;
                }

                // Calculate a score for how well each item in the target camp matches the prototype item
                $matches = array_map(function (ChecklistItem $existingItem) use ($itemPrototype) {
                    $score = 0;
                    if ($existingItem->text !== $itemPrototype->text) {
                        return $score;
                    }
                    ++$score;

                    /** @var ChecklistItem $parent */
                    $parent = $itemPrototype->getParent();

                    /** @var ChecklistItem $existingParent */
                    $existingParent = $existingItem->getParent();
                    while (null !== $parent && null !== $existingParent && $score <= ChecklistItem::MAX_NESTING_DEPTH) {
                        if ($existingParent->text !== $parent->text) {
                            return $score;
                        }
                        ++$score;

                        /** @var ChecklistItem $parent */
                        $parent = $parent->getParent();
                    }

                    if ($existingItem->checklist->checklistPrototypeId !== $itemPrototype->checklist->checklistPrototypeId) {
                        return $score;
                    }
                    ++$score;

                    if ($existingItem->checklist->name !== $itemPrototype->checklist->name) {
                        return $score;
                    }

                    return $score + 1;
                }, $checklistItemsInCamp);

                // Use the checklist with the largest positive score, if any
                $maxScore = max([0, ...$matches]);
                $bestMatchIndex = array_find_key($matches, function ($match) use ($maxScore) {
                    return $match === $maxScore;
                });
                if ($maxScore > 0 && null !== $bestMatchIndex) {
                    $result = $checklistItemsInCamp[$bestMatchIndex];
                    $entityMap->add($itemPrototype, $result);
                    $this->addChecklistItem($result);
                }
            }
        }
    }
}
