<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

/**
 * This class is not mapped as an API resource.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class NotAResource {
    public function __construct(
        private $foo,
        private $bar
    ) {}

    public function getFoo() {
        return $this->foo;
    }

    public function getBar() {
        return $this->bar;
    }
}
