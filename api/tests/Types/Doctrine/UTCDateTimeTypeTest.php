<?php
/**
 * mostly copied 1:1 from https://github.com/doctrine/dbal/blob/3.2.x/tests/Types/DateTimeTest.php.
 */

namespace App\Tests\Types\Doctrine;

use App\Types\Doctrine\UTCDateTimeType;
use DateTime;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @internal
 */
class UTCDateTimeTypeTest extends BaseDateTypeTestCase {
    protected function setUp(): void {
        $this->type = new UTCDateTimeType();

        parent::setUp();
    }

    public function testDateTimeConvertsToDatabaseValue(): void {
        $date = new DateTime('1985-09-01 10:10:10');

        $expected = $date->format($this->platform->getDateTimeTzFormatString());
        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        self::assertEquals($expected, $actual);
    }

    public function testDateTimeConvertsToPHPValue(): void {
        // Birthday of jwage and also birthday of Doctrine. Send him a present ;)
        $date = $this->type->convertToPHPValue('1985-09-01 00:00:00', $this->platform);
        self::assertInstanceOf(DateTime::class, $date);
        self::assertEquals('1985-09-01 00:00:00', $date->format('Y-m-d H:i:s'));
    }

    public function testInvalidDateTimeFormatConversion(): void {
        $this->expectException(ConversionException::class);
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * removed the following tests (non-matching formats are not possible with our implementation).
     */
    /*
    public function testConvertsNonMatchingFormatToPhpValueWithParser(): void {
        $date = '1985/09/01 10:10:10.12345';

        $actual = $this->type->convertToPHPValue($date, $this->platform);

        self::assertEquals('1985-09-01 10:10:10', $actual->format('Y-m-d H:i:s'));
    }*/

    /**
     * additional tests to cover UTC conversion.
     */
    public function testConvertsPHPValueToUTC(): void {
        // set timezone to anything else than UTC
        date_default_timezone_set('America/New_York');

        $date = new DateTime('1985-09-01 10:10:10+02:00');

        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        self::assertEquals('1985-09-01 08:10:10', $actual);
    }

    public function testInterpretsDatabaseValueAsUTC(): void {
        // set timezone to anything else than UTC
        date_default_timezone_set('America/New_York');

        $date = $this->type->convertToPHPValue('1985-09-01 00:00:00', $this->platform);

        self::assertInstanceOf(DateTime::class, $date);
        self::assertEquals('1985-09-01T00:00:00+00:00', $date->format('c'));
    }
}
