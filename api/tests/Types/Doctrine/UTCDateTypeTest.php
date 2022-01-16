<?php
/**
 * mostly copied from https://github.com/doctrine/dbal/blob/3.2.x/tests/Types/DateTest.php.
 */

namespace App\Tests\Types\Doctrine;

use App\Types\Doctrine\UTCDateType;
use function date_default_timezone_set;
use DateTime;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @internal
 */
class UTCDateTypeTest extends BaseDateTypeTestCase {
    protected function setUp(): void {
        $this->type = new UTCDateType();

        parent::setUp();
    }

    public function testDateConvertsToPHPValue(): void {
        // Birthday of jwage and also birthday of Doctrine. Send him a present ;)
        self::assertInstanceOf(
            DateTime::class,
            $this->type->convertToPHPValue('1985-09-01', $this->platform)
        );
    }

    public function testDateResetsNonDatePartsToZeroUnixTimeValues(): void {
        $date = $this->type->convertToPHPValue('1985-09-01', $this->platform);

        self::assertEquals('00:00:00', $date->format('H:i:s'));
    }

    public function testDateRestsSummerTimeAffection(): void {
        date_default_timezone_set('Europe/Berlin');

        $date = $this->type->convertToPHPValue('2009-08-01', $this->platform);
        self::assertEquals('00:00:00', $date->format('H:i:s'));
        self::assertEquals('2009-08-01', $date->format('Y-m-d'));

        $date = $this->type->convertToPHPValue('2009-11-01', $this->platform);
        self::assertEquals('00:00:00', $date->format('H:i:s'));
        self::assertEquals('2009-11-01', $date->format('Y-m-d'));
    }

    public function testInvalidDateFormatConversion(): void {
        $this->expectException(ConversionException::class);
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * additional tests to cover UTC conversion.
     */
    public function testConvertsPHPValueToUTC(): void {
        // set timezone to anything else than UTC
        date_default_timezone_set('America/New_York');

        $date = new DateTime('1985-09-02 00:00:00 +02:00'); // is still 1985-09-01 22:00 in UTC time

        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        self::assertEquals('1985-09-01', $actual);
    }

    public function testInterpretsDatabaseValueAsUTC(): void {
        // set timezone to anything else than UTC
        date_default_timezone_set('America/New_York');

        $date = $this->type->convertToPHPValue('1985-09-01', $this->platform);

        self::assertEquals('1985-09-01T00:00:00+00:00', $date->format('c'));
    }
}
