<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MaterialItem;
use App\State\Util\AbstractPersistProcessor;

/**
 * @template-extends AbstractPersistProcessor<MaterialItem>
 */
class MaterialItemCreateProcessor extends AbstractPersistProcessor {
    public function __construct(ProcessorInterface $decorated) {
        parent::__construct($decorated);
    }

    /**
     * @param MaterialItem $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): MaterialItem {
        $data->camp = $data->getCamp();

        return $data;
    }
}
