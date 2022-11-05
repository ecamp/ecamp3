<?php

namespace App\DataPersister\Util;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\BaseEntity;

abstract class AbstractDataPersister implements ContextAwareDataPersisterInterface {
    /**
     * @param CustomActionListener[]   $customActionListeners
     * @param PropertyChangeListener[] $propertyChangeListeners
     */
    public function __construct(
        private string $classname,
        private DataPersisterObservable $dataPersisterObservable,
        private array $customActionListeners = [],
        private array $propertyChangeListeners = []
    ) {
        foreach ($this->customActionListeners as $listener) {
            if (!$listener instanceof CustomActionListener) {
                throw new \InvalidArgumentException('customActionListeners must be of type '.CustomActionListener::class);
            }
        }
        foreach ($this->propertyChangeListeners as $listener) {
            if (!$listener instanceof PropertyChangeListener) {
                throw new \InvalidArgumentException('propertyChangeListeners must be of type '.PropertyChangeListener::class);
            }
        }
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof $this->classname) && $this->dataPersisterObservable->supports($data, $context);
    }

    /**
     * @return object
     */
    public function persist($data, array $context = []) {
        $observable = $this->dataPersisterObservable
            ->onBeforeCreate(fn ($data) => $this->beforeCreate($data))
            ->onAfterCreate(fn ($data) => $this->afterCreate($data))
            ->onBeforeUpdate(fn ($data) => $this->beforeUpdate($data))
            ->onAfterUpdate(fn ($data) => $this->afterUpdate($data))
        ;

        foreach ($this->customActionListeners as $customActionListener) {
            $observable->onCustomAction($customActionListener);
        }
        foreach ($this->propertyChangeListeners as $propertyChangeListener) {
            $observable->onPropertyChange($propertyChangeListener);
        }

        return $observable
            ->persist($data, $context)
        ;
    }

    public function remove($data, array $context = []) {
        $this->dataPersisterObservable
            ->onBeforeRemove(fn ($data) => $this->beforeRemove($data))
            ->onAfterRemove(fn ($data) => $this->afterRemove($data))
            ->remove($data, $context)
        ;
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
    public function beforeCreate($data): BaseEntity {
        return $data;
    }

    /**
     * For side effects after the creation of an object.
     */
    public function afterCreate($data): void {
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
    public function beforeUpdate($data) {
        return $data;
    }

    /**
     * For side effects after an update.
     */
    public function afterUpdate($data): void {
    }

    /**
     * Hook before the removal of an object.
     * Return null if the object should be deleted.
     * Return not null if you want to update the object instead.
     * Throw an Exception if this should never happen.
     */
    public function beforeRemove($data): ?BaseEntity {
        return null;
    }

    /**
     * For side effects after removal of an object.
     */
    public function afterRemove($data): void {
    }
}
