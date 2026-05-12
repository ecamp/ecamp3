<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ChecklistNode;
use App\Repository\ChecklistItemRepository;

/**
 * @template-extends ContentNodePersistProcessor<ChecklistNode>
 */
class ChecklistNodePersistProcessor extends ContentNodePersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly ChecklistItemRepository $checklistItemRepository,
    ) {
        parent::__construct($decorated);
    }

    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): ChecklistNode {
        /** @var ChecklistNode $data */
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        $allIds = array_unique(array_merge($data->addChecklistItemIds ?? [], $data->removeChecklistItemIds ?? []));
        $checklistItems = [];
        if (!empty($allIds)) {
            $fetchedItems = $this->checklistItemRepository->findBy(['id' => $allIds]);
            foreach ($fetchedItems as $item) {
                $checklistItems[$item->getId()] = $item;
            }
        }

        if (null !== $data->addChecklistItemIds) {
            foreach ($data->addChecklistItemIds as $checklistItemId) {
                if (isset($checklistItems[$checklistItemId])) {
                    // if a checklistItem does not exists, do not add it
                    $data->addChecklistItem($checklistItems[$checklistItemId]);
                }
            }
        }
        if (null !== $data->removeChecklistItemIds) {
            foreach ($data->removeChecklistItemIds as $checklistItemId) {
                if (isset($checklistItems[$checklistItemId])) {
                    // if a checklistItem no longer exists, it does not have to be removed
                    $data->removeChecklistItem($checklistItems[$checklistItemId]);
                }
            }
        }

        return $data;
    }
}
