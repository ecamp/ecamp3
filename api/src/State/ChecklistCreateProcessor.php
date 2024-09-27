<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Checklist;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;

/**
 * @template-extends AbstractPersistProcessor<Checklist>
 */
class ChecklistCreateProcessor extends AbstractPersistProcessor {
    public function __construct(ProcessorInterface $decorated) {
        parent::__construct($decorated);
    }

    /**
     * @param Checklist $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Checklist {
        if (isset($data->copyChecklistSource)) {
            // CopyChecklist Source is set -> copy it's content
            $entityMap = new EntityMap();
            $data->copyFromPrototype($data->copyChecklistSource, $entityMap);
        }

        return $data;
    }
}
