<?php

namespace App\Types\Doctrine;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType {
    private static ?DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (null === $value) {
            return null;
        }

        if ($value instanceof DateTime) {
            $value->setTimeZone(self::getUtc());

            return $value->format($platform->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if (null === $value || $value instanceof DateTimeInterface) {
            return $value;
        }

        $val = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::getUtc());

        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $val;
    }

    private static function getUtc(): DateTimeZone {
        return self::$utc ?: self::$utc = new DateTimeZone('UTC');
    }
}
