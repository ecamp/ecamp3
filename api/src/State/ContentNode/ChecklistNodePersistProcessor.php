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
        private ChecklistItemRepository $checklistItemRepository,
    ) {
        parent::__construct($decorated);
    }

    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): ChecklistNode {
        /** @var ChecklistNode $data */
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        if (null !== $data->addChecklistItemIds) {
            foreach ($data->addChecklistItemIds as $checklistItemId) {
                $checklistItem = $this->checklistItemRepository->find($checklistItemId);
                if (null != $checklistItem) {
                    // if a checklistItem does not exists, do not add it
                    $data->addChecklistItem($checklistItem);
                }
            }
        }
        if (null !== $data->removeChecklistItemIds) {
            foreach ($data->removeChecklistItemIds as $checklistItemId) {
                $checklistItem = $this->checklistItemRepository->find($checklistItemId);
                if (null != $checklistItem) {
                    // if a checklistItem no longer exists, it does not have to be removed
                    $data->removeChecklistItem($checklistItem);
                }
            }
        }

        return $data;
    }
}
