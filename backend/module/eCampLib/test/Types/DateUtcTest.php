<?php

namespace eCamp\LibTest\Hydrator;

use eCamp\Lib\Types\DateUtc;
use eCamp\Lib\Types\InvalidDateFormatException;
use eCamp\Lib\Types\InvalidZoneOffsetException;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class DateUtcTest extends AbstractTestCase {
    const TEST_DATE = '2020-07-01';
    const TEST_DATETIME = '2020-07-01T00:00+00:00';
    const TEST_DATETIME_CUSTOMFORMAT = '2020-07-01 00:00:00';

    public function testParsing() {
        $date = new DateUtc(self::TEST_DATE);
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATE));
    }

    public function testParsingFail() {
        $this->expectException(InvalidDateFormatException::class);
        new DateUtc(self::TEST_DATETIME);
    }

    public function testTimezoneFail() {
        $this->expectException(InvalidZoneOffsetException::class);
        new DateUtc(self::TEST_DATE, new \DateTimeZone('GMT'));
    }

    public function testParsingCustom() {
        $date = new DateUtc(self::TEST_DATETIME_CUSTOMFORMAT, null, 'Y-m-d H:i:s');
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATE));
    }
}
