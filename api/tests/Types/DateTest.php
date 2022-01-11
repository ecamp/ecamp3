<?php

namespace App\Tests\Types;

use App\Types\Date;
use DateTime;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
class DateTest extends TestCase {
    public const TEST_DATE = '2020-07-01';
    public const TEST_DATETIME = '2020-07-01T00:00+00:00';
    public const TEST_DATETIME_CUSTOMFORMAT = '2020-07-01 00:00:00';

    public function testParsing(): void {
        $date = new Date(self::TEST_DATE);

        $this->assertEquals(new DateTime(self::TEST_DATETIME), $date);
        $this->assertThat($date->__toString(), self::equalTo(self::TEST_DATE));
    }

    public function testParsingFail(): void {
        $this->expectException(RuntimeException::class);
        new Date(self::TEST_DATETIME);
    }

    public function testTimezoneFail(): void {
        $this->expectException(RuntimeException::class);
        new Date(self::TEST_DATE, new \DateTimeZone('GMT'));
    }

    public function testEmptyTimeReturnsToday(): void {
        $date = new Date();
        $this->assertEquals(new DateTime('today', new \DateTimeZone('UTC')), $date);
    }

    public function testImplementsJsonSerializable(): void {
        $date = new Date(self::TEST_DATE);

        $this->assertInstanceOf(JsonSerializable::class, $date);
        $this->assertEquals(self::TEST_DATE, $date->jsonSerialize());
    }
}
