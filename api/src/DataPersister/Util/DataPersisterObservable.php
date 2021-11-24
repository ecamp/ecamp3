<?php

namespace App\DataPersister\Util;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Closure;
use Symfony\Component\HttpFoundation\RequestStack;

class DataPersisterObservable {
    private Closure $onBeforeCreate;
    private Closure $onAfterCreate;
    private Closure $onBeforeUpdate;
    private Closure $onAfterUpdate;
    private Closure $onBeforeRemove;
    private Closure $onAfterRemove;
    /**
     * @var CustomActionListener[]
     */
    private array $customActionListeners = [];
    /**
     * @var PropertyChangeListener[]
     */
    private array $propertyChangedListeners = [];

    public function __construct(
        private ContextAwareDataPersisterInterface $dataPersister,
        private RequestStack $requestStack
    ) {
        $identity = fn ($data) => $data;
        $noop = function ($data) {
        };
        $this->onBeforeCreate = $identity;
        $this->onAfterCreate = $noop;
        $this->onBeforeUpdate = $identity;
        $this->onAfterUpdate = $noop;
        $this->onBeforeRemove = $identity;
        $this->onAfterRemove = $noop;
    }

    public function supports($data, array $context): bool {
        return $this->dataPersister->supports($data, $context);
    }

    public function onBeforeCreate(Closure $onBeforeCreate): self {
        $this->onBeforeCreate = $onBeforeCreate;

        return $this;
    }

    public function onAfterCreate(Closure $onAfterCreate): self {
        $this->onAfterCreate = $onAfterCreate;

        return $this;
    }

    public function onBeforeUpdate(Closure $onBeforeUpdate): self {
        $this->onBeforeUpdate = $onBeforeUpdate;

        return $this;
    }

    public function onAfterUpdate(Closure $onAfterUpdate): self {
        $this->onAfterUpdate = $onAfterUpdate;

        return $this;
    }

    public function onBeforeRemove(Closure $onBeforeRemove): self {
        $this->onBeforeRemove = $onBeforeRemove;

        return $this;
    }

    public function onAfterRemove(Closure $onAfterRemove): self {
        $this->onAfterRemove = $onAfterRemove;

        return $this;
    }

    public function onCustomAction(CustomActionListener $customActionListener): self {
        $this->customActionListeners[] = $customActionListener;

        return $this;
    }

    public function onPropertyChange(PropertyChangeListener $propertyChangeListener): self {
        $this->propertyChangedListeners[] = $propertyChangeListener;

        return $this;
    }

    public function persist($data, array $context = []): object {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            $data = call_user_func($this->onBeforeCreate, $data);
        } elseif ('patch' === ($context['item_operation_name'] ?? null)) {
            $data = call_user_func($this->onBeforeUpdate, $data);
        } else {
            foreach ($this->customActionListeners as $listener) {
                if (self::actionNameMatches($listener, $context)) {
                    $data = call_user_func($listener->getBeforeAction(), $data);

                    break;
                }
            }
        }

        $dataBefore = $this->requestStack->getCurrentRequest()->attributes->get('previous_data');
        if (null != $dataBefore) {
            foreach ($this->propertyChangedListeners as $listener) {
                $propertyBefore = call_user_func($listener->getExtractProperty(), $dataBefore);
                $propertyNow = call_user_func($listener->getExtractProperty(), $data);
                if ($propertyBefore !== $propertyNow) {
                    $data = call_user_func($listener->getBeforeAction(), $data);
                }
            }
        }

        $data = $this->dataPersister->persist($data, $context);

        if ('post' === ($context['collection_operation_name'] ?? null)) {
            call_user_func($this->onAfterCreate, $data);
        } elseif ('patch' === ($context['item_operation_name'] ?? null)) {
            call_user_func($this->onAfterUpdate, $data);
        } else {
            foreach ($this->customActionListeners as $listener) {
                if (self::actionNameMatches($listener, $context)) {
                    call_user_func($listener->getAfterAction(), $data);

                    break;
                }
            }
        }

        if (null != $dataBefore) {
            foreach ($this->propertyChangedListeners as $listener) {
                $propertyBefore = call_user_func($listener->getExtractProperty(), $dataBefore);
                $propertyNow = call_user_func($listener->getExtractProperty(), $data);
                if ($propertyBefore !== $propertyNow) {
                    call_user_func($listener->getAfterAction(), $data);
                }
            }
        }

        return $data;
    }

    public function remove($data, array $context = []) {
        $beforeRemoveResult = call_user_func($this->onBeforeRemove, $data);
        if (null == $beforeRemoveResult) {
            $this->dataPersister->remove($data, $context);
        } else {
            $this->dataPersister->persist($data, $context);
        }
        call_user_func($this->onAfterRemove, $data);
    }

    private static function actionNameMatches(CustomActionListener $listener, mixed $context): bool {
        return $listener->getActionName() === ($context['item_operation_name'] ?? null)
                || $listener->getActionName() === ($context['collection_operation_name'] ?? null);
    }
}
