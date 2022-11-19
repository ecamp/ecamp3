<?php

namespace App\State\Util;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

abstract class AbstractRemoveProcessor implements ProcessorInterface {
    public function __construct(
        private ProcessorInterface $decorated,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->onBefore($data, $operation, $uriVariables, $context);
        $this->decorated->process($data, $operation, $uriVariables, $context);
        $this->onAfter($data, $operation, $uriVariables, $context);
    }

    /**
     * Hook before the removal of an object.
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
    }

    /**
     * For side effects after removal of an object.
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
    }
}
