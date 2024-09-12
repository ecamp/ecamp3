<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Checklist;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

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
        if (!$data->isPrototype && null == $data->camp) {
            throw new ValidationException(new ConstraintViolationList([
                new ConstraintViolation('This value should not be null.', '', [], null, 'camp', null, null, null, new NotNull()),
            ]));
        }
        if ($data->isPrototype && null !== $data->camp) {
            throw new ValidationException(new ConstraintViolationList([
                new ConstraintViolation('This value should be null.', '', [], null, 'camp', null, null, null, new IsNull()),
            ]));
        }

        if (isset($data->copyChecklistSource)) {
            // CopyChecklist Source is set -> copy it's content
            $entityMap = new EntityMap();
            $data->copyFromPrototype($data->copyChecklistSource, $entityMap);
        }

        return $data;
    }
}
