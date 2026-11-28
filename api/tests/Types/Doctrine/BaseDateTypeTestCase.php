<?php

/**
 * copied 1:1 from https://github.com/doctrine/dbal/blob/4.2.x/tests/Types/BaseDateTypeTestCase.php.
 */

namespace App\Tests\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class BaseDateTypeTestCase extends TestCase {
    protected AbstractPlatform $platform;
    protected Type $type;

    /** @var non-empty-string */
    private string $currentTimezone;

    protected function setUp(): void {
        $this->platform = new PostgreSQLPlatform();
        $this->currentTimezone = \date_default_timezone_get();
    }

    protected function tearDown(): void {
        \date_default_timezone_set($this->currentTimezone);
    }

    public function testDateConvertsToDatabaseValue(): void {
        self::assertIsString($this->type->convertToDatabaseValue(new \DateTimeImmutable(), $this->platform));
    }

    #[DataProvider('invalidPHPValuesProvider')]
    public function testInvalidTypeConversionToDatabaseValue(mixed $value): void {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue($value, $this->platform);
    }

    /** @return \Iterator<(int|string), array<mixed>> */
    public static function invalidPHPValuesProvider(): \Iterator {
        yield [0];

        yield [''];

        yield ['foo'];

        yield ['10:11:12'];

        yield ['2015-01-31'];

        yield ['2015-01-31 10:11:12'];

        yield [new \stdClass()];

        yield [27];

        yield [-1];

        yield [1.2];

        yield [[]];

        yield [['an array']];
    }

    public function testNullConversion(): void {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertDateTimeToPHPValue(): void {
        $date = new \DateTimeImmutable('now');

        self::assertSame($date, $this->type->convertToPHPValue($date, $this->platform));
    }
}
