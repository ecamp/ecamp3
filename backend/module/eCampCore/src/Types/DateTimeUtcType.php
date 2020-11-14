<?php

namespace eCamp\Core\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class DateTimeUtcType extends DateTimeType {
    /**
     * @param mixed $value
     *
     * @throws ConversionException
     * @throws \Exception
     *
     * @return null|\DateTime|\DateTimeInterface|mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if (null === $value || $value instanceof DateTimeUTC) {
            return $value;
        }

        $converted = new DateTimeUTC($value);

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted;
    }
}
