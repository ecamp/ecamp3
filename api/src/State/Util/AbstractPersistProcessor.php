<?php

namespace App\State\Util;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use InvalidArgumentException;

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
                throw new InvalidArgumentException('propertyChangeListeners must be of type '.PropertyChangeListener::class);
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
        $this->onBefore($data);

        $dataBefore = $context['previous_data'];
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

        $this->onAfter($data);

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
     * @template T of BaseEntity
     *
     * @param T $data
     *
     * @return T
     */
    public function onBefore($data): BaseEntity {
        return $data;
    }

    /**
     * For side effects after processing the object.
     */
    public function onAfter($data): void {
    }
}
