<?php

namespace eCamp\LibTest\Hydrator;

use eCamp\Lib\Hydrator\Util;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class UtilTest extends AbstractTestCase {
    public function testTimestamp() {
        $date = Util::parseDate(949449600000);
        $date = Util::parseDate($date);
        $dateStr = Util::extractTimestamp($date);

        $this->assertEquals(949449600000, $dateStr);
    }
}
