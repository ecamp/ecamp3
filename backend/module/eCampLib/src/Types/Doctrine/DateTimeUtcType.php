<?php

namespace eCamp\Lib\Types\Doctrine;

use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use eCamp\Lib\Types\DateTimeUtc;

class DateTimeUtcType extends DateTimeType {
    private static ?DateTimeZone $utc = null;

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @throws ConversionException
     * @throws \Exception
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeUtc {
        if (null === $value || $value instanceof DateTimeUtc) {
            return $value;
        }

        $converted = new DateTimeUtc($value, self::getUtc(), $platform->getDateTimeFormatString());

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted;
    }

    private static function getUtc(): DateTimeZone {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }
}
