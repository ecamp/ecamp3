<?php

namespace eCamp\Core\Types;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;
use Exception;

class DateUtcType extends DateType {
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

        $val = new DateUtc($value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        return $val;
    }
}
