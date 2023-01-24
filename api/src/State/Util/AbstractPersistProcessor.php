<?php

namespace App\State\Util;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;

abstract class AbstractPersistProcessor implements ProcessorInterface {
    /**
     * @param PropertyChangeListener[] $propertyChangeListeners
     */
    public function __construct(
        private ProcessorInterface $decorated,
        private array $propertyChangeListeners = []
    ) {
        foreach ($propertyChangeListeners as $listener) {
            if (!$listener instanceof PropertyChangeListener) {
                throw new \InvalidArgumentException('propertyChangeListeners must be of type '.PropertyChangeListener::class);
            }
        }
    }

    /**
     * @template T
     *
     * @param T $data
     *
     * @return T
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []) {
        $data = $this->onBefore($data, $operation, $uriVariables, $context);

        $dataBefore = $context['previous_data'] ?? null;
        if (null != $dataBefore) {
            foreach ($this->propertyChangeListeners as $listener) {
                $propertyBefore = call_user_func($listener->getExtractProperty(), $dataBefore);
                $propertyNow = call_user_func($listener->getExtractProperty(), $data);
                if ($propertyBefore !== $propertyNow) {
                    $data = call_user_func($listener->getBeforeAction(), $data);
                }
            }
        }

        $data = $this->decorated->process($data, $operation, $uriVariables, $context);

        $this->onAfter($data, $operation, $uriVariables, $context);

        if (null != $dataBefore) {
            foreach ($this->propertyChangeListeners as $listener) {
                $propertyBefore = call_user_func($listener->getExtractProperty(), $dataBefore);
                $propertyNow = call_user_func($listener->getExtractProperty(), $data);
                if ($propertyBefore !== $propertyNow) {
                    call_user_func($listener->getAfterAction(), $data);
                }
            }
        }

        return $data;
    }

    /**
     * Return an object of the type and with the properties you want persisted.
     *
     * @template T of BaseEntity
     *
     * @param T $data
     *
     * @return T
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): BaseEntity {
        return $data;
    }

    /**
     * For side effects after processing the object.
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
    }
}
