<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Checklist;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;

/**
 * @template-extends AbstractPersistProcessor<Checklist>
 */
class ChecklistCreateProcessor extends AbstractPersistProcessor {
    /**
     * @param Checklist $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Checklist {
        if (null !== $data->copyChecklistSource) {
            // CopyChecklist Source is set -> copy it's content
            $entityMap = new EntityMap($data->camp);
            $data->copyFromPrototype($data->copyChecklistSource, $entityMap);
        }

        return $data;
    }
}
