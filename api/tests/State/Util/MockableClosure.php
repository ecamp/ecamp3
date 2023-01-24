<?php

namespace App\Tests\State\Util;

/**
 * Interface that it's possible to mock closures.
 * The internal \Closure class is final and cannot be mocked.
 */
interface MockableClosure {
    public function call($data): mixed;
}
