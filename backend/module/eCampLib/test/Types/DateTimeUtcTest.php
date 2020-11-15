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

    public function testTimezoneDifferent() {
        $initial_timezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $dateUTC = new DateTimeUtc();
        date_default_timezone_set('Europe/Zurich');
        $dateZurich = new DateTimeUtc();
        $this->assertThat($dateZurich->format('G'), self::equalTo($dateUTC->format('G')));
        date_default_timezone_set($initial_timezone);
    }

    public function testTimezoneDifferentNative() {
        $initial_timezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $dateUTC = new \DateTime();
        date_default_timezone_set('Europe/Zurich');
        $dateZurich = new \DateTime();
        $this->assertThat($dateZurich->format('G'), self::equalTo(intval($dateUTC->format('G')) + 1 % 23));
        date_default_timezone_set($initial_timezone);
    }
}
