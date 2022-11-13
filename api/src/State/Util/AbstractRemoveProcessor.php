<?php

namespace App\State\Util;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;

abstract class AbstractRemoveProcessor implements ProcessorInterface {
    public function __construct(
        private ProcessorInterface $decorated,
    ) {
    }

    /**
     * @template T
     *
     * @param T $data
     *
     * @return T
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->onBefore($data, $operation, $uriVariables, $context);
        $this->decorated->process($data, $operation, $uriVariables, $context);
        $this->onAfter($data, $operation, $uriVariables, $context);
    }

    /**
     * Hook before the removal of an object.
     *
     * @template T of BaseEntity
     *
     * @param T $data
     *
     * @return T
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
    }

    /**
     * For side effects after removal of an object.
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
    }
}
