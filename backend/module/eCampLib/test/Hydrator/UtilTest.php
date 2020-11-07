<?php

namespace eCamp\LibTest\Hydrator;

use eCamp\Lib\Hydrator\Util;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class UtilTest extends AbstractTestCase {
    public function testTimestamp() {
        $date = Util::parseDate('2020-07-01');
        $dateStr = Util::extractDateTime($date);
        $this->assertEquals('2020-07-01T00:00:00+00:00', $dateStr);
    }
}
