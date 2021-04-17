<?php

namespace eCamp\LibTest\Hydrator;

use DateInterval;
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

    private $initialTimeZone;

    protected function setUp(): void {
        $this->initialTimeZone = date_default_timezone_get();
    }

    protected function tearDown(): void {
        date_default_timezone_set($this->initialTimeZone);
    }

    public function testParsing(): void {
        $date = new DateTimeUtc(self::TEST_DATETIME);
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATETIME));
    }

    public function testParsingFail(): void {
        $this->expectException(InvalidDateFormatException::class);
        new DateTimeUtc(self::TEST_DATE);
    }

    public function testTimezoneFail(): void {
        $this->expectException(InvalidZoneOffsetException::class);
        new DateTimeUtc(self::TEST_DATETIME_TIMEZONE);
    }

    public function testParsingCustom(): void {
        $date = new DateTimeUtc(self::TEST_DATETIME_CUSTOMFORMAT, null, 'Y-m-d H:i:s');
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATETIME));
    }

    public function testTimezoneDifferent(): void {
        date_default_timezone_set('UTC');
        $dateUTC = new DateTimeUtc();
        date_default_timezone_set('CET');
        $dateZurich = new DateTimeUtc();
        $this->assertThat($dateZurich->format('G'), self::equalTo($dateUTC->format('G')));
    }

    public function testTimezoneDifferentNative(): void {
        date_default_timezone_set('UTC');
        $dateUTC = new \DateTime();
        date_default_timezone_set('CET');
        $dateZurich = new \DateTime();
        $this->assertThat(
            $dateZurich->diff($dateUTC)->format('h'),
            self::equalTo(oneHour()->format('h'))
        );
    }
}

function oneHour(): DateInterval {
    return new DateInterval('PT1H');
}
