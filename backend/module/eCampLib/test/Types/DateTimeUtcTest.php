<?php

namespace eCamp\LibTest\Hydrator;

use eCamp\Lib\Types\DateTimeUtc;
use eCamp\Lib\Types\InvalidDateFormatException;
use eCamp\Lib\Types\InvalidZoneOffsetException;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class DateTimeUtcTest extends AbstractTestCase {
    const TEST_DATE = '2020-07-01';
    const TEST_DATETIME = '2020-07-01T00:00+00:00';
    const TEST_DATETIME_TIMEZONE = '2020-07-01T00:00+01:00';
    const TEST_DATETIME_CUSTOMFORMAT = '2020-07-01 00:00:00';

    public function testParsing() {
        $date = new DateTimeUtc(self::TEST_DATETIME);
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATETIME));
    }

    public function testParsingFail() {
        $this->expectException(InvalidDateFormatException::class);
        new DateTimeUtc(self::TEST_DATE);
    }

    public function testTimezoneFail() {
        $this->expectException(InvalidZoneOffsetException::class);
        new DateTimeUtc(self::TEST_DATETIME_TIMEZONE);
    }

    public function testParsingCustom() {
        $date = new DateTimeUtc(self::TEST_DATETIME_CUSTOMFORMAT, null, 'Y-m-d H:i:s');
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATETIME));
    }
}
