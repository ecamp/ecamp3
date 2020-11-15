<?php

namespace eCamp\Lib\Types\Doctrine;

use DateTime;
use DateTimeInterface;
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
     * @param mixed $value
     *
     * @throws ConversionException
     * @throws Exception
     *
     * @return null|DateTime|DateTimeInterface|mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
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
