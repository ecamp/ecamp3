<?php

namespace App\State\Util;

use Closure;
use ReflectionFunction;

class PropertyChangeListener {
    private function __construct(
        private Closure $extractProperty,
        private Closure $beforeAction,
        private Closure $afterAction
    ) {
    }

    /**
     * @throws \ReflectionException
     */
    public static function of(
        Closure $extractProperty,
        ?Closure $beforeAction = null,
        ?Closure $afterAction = null
    ): PropertyChangeListener {
        if (null == $beforeAction) {
            $beforeAction = function ($data) {
            };
        }
        if (null == $afterAction) {
            $afterAction = function ($data) {
            };
        }
        if (self::hasOneParameter($extractProperty)) {
            throw new \InvalidArgumentException('extractProperty must have exactly one parameter');
        }
        if (self::hasOneParameter($beforeAction)) {
            throw new \InvalidArgumentException('afterAction must have exactly one parameter');
        }
        if (self::hasOneParameter($afterAction)) {
            throw new \InvalidArgumentException('afterAction must have exactly one parameter');
        }

        return new PropertyChangeListener($extractProperty, $beforeAction, $afterAction);
    }

    public function getExtractProperty(): Closure {
        return $this->extractProperty;
    }

    public function getBeforeAction(): Closure {
        return $this->beforeAction;
    }

    public function getAfterAction(): Closure {
        return $this->afterAction;
    }

    /**
     * @throws \ReflectionException
     */
    private static function hasOneParameter(?Closure $beforeAction): bool {
        $beforeActionReflectionFunction = new ReflectionFunction($beforeAction);

        return 1 != $beforeActionReflectionFunction->getNumberOfParameters();
    }
}
