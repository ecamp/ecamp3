<?php

namespace eCamp\LibTest\Hydrator;

use eCamp\Lib\Hydrator\Util;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class UtilTest extends AbstractTestCase {
    public function testDate() {
        $date = Util::parseDate('2000-01-01');
        $date = Util::parseDate($date);
        $dateStr = Util::extractDate($date);

        $this->assertEquals('2000-01-01', $dateStr);
    }

    public function testDateTime() {
        $date = Util::parseDate('2000-01-01 14:01:02');
        $date = Util::parseDate($date);
        $dateStr = Util::extractDateTime($date);

        $this->assertEquals('2000-01-01 14:01:02', $dateStr);
    }
}
