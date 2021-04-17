<?php

namespace eCamp\Lib\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;
use eCamp\Lib\Types\DateUtc;
use Exception;

class DateUtcType extends DateType {
    /**
     * @var \DateTimeZone
     */
    private static $utc;

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
     * @throws Exception
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateUtc {
        if (null === $value || $value instanceof DateUtc) {
            return $value;
        }

        $val = new DateUtc($value, self::getUtc(), $platform->getDateFormatString());
        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        return $val;
    }

    private static function getUtc(): \DateTimeZone {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }
}
