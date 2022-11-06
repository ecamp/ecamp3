<?php

namespace App\DataPersister\Util;

class CustomActionListener {
    private function __construct(
        private string $actionName,
        private \Closure $beforeAction,
        private \Closure $afterAction
    ) {
    }

    /**
     * @throws \ReflectionException
     */
    public static function of(
        string $actionName,
        ?\Closure $beforeAction = null,
        ?\Closure $afterAction = null
    ): CustomActionListener {
        if (null == $beforeAction) {
            $beforeAction = fn ($data) => $data;
        }
        if (null == $afterAction) {
            $afterAction = function ($data) {
            };
        }
        if (self::hasOneParameter($beforeAction)) {
            throw new \InvalidArgumentException('beforeAction must have exactly one parameter');
        }
        if (self::hasOneParameter($afterAction)) {
            throw new \InvalidArgumentException('afterAction must have exactly one parameter');
        }

        return new CustomActionListener($actionName, $beforeAction, $afterAction);
    }

    public function getActionName(): string {
        return $this->actionName;
    }

    public function getBeforeAction(): \Closure {
        return $this->beforeAction;
    }

    public function getAfterAction(): \Closure {
        return $this->afterAction;
    }

    /**
     * @throws \ReflectionException
     */
    private static function hasOneParameter(?\Closure $beforeAction): bool {
        $beforeActionReflectionFunction = new \ReflectionFunction($beforeAction);

        return 1 != $beforeActionReflectionFunction->getNumberOfParameters();
    }
}
