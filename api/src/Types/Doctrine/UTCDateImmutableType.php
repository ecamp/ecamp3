<?php

namespace App\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;

/**
 * Variant of Doctrine's DateImmutableType that converts to UTC before storing
 * and interprets stored dates as midnight UTC, independent of the PHP default time zone.
 */
class UTCDateImmutableType extends DateImmutableType {
    private static ?\DateTimeZone $utc = null;

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->setTimezone(self::getUtc())->format($platform->getDateFormatString());
        }

        throw InvalidType::new($value, static::class, ['null', \DateTimeImmutable::class]);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTimeImmutable {
        if (null === $value || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('!'.$platform->getDateFormatString(), $value, self::getUtc());
        if (false !== $dateTime) {
            return $dateTime;
        }

        throw InvalidFormat::new($value, static::class, $platform->getDateFormatString());
    }

    private static function getUtc(): \DateTimeZone {
        return self::$utc ??= new \DateTimeZone('UTC');
    }
}
