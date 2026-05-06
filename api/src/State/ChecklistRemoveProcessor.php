<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Checklist;
use App\State\Util\AbstractRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends AbstractRemoveProcessor<Checklist>
 */
class ChecklistRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Checklist $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $camp = $data->camp;
        if (null === $camp || $data->isPrototype) {
            return;
        }

        $camp->hasChecklists = 1 < $this->em->getRepository(Checklist::class)->count([
            'camp' => $camp,
            'isPrototype' => false,
        ]);
    }
}
