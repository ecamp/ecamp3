<?php

namespace eCamp\LibTest\Util;

use eCamp\Lib\Util\IdGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class IdGeneratorTest extends TestCase {
    public function testGenerateRandomHexString(): void {
        $str = IdGenerator::generateRandomHexString(12);

        $this->assertEquals(12, strlen($str));
    }
}
