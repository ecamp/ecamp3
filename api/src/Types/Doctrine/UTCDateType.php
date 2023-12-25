<?php

namespace App\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateType;

class UTCDateType extends DateType {
    private static ?\DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     *
     * @psalm-param T $value
     *
     * @return (T is null ? null : string)
     *
     * @template T
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            $value = $value->setTimezone(self::getUtc());
        }

        if ($value instanceof \DateTimeImmutable) {
            return (new DateImmutableType())->convertToDatabaseValue($value, $platform);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     *
     * @param T $value
     *
     * @return (T is null ? null : \DateTimeInterface)
     *
     * @throws ConversionException
     *
     * @template T
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTimeInterface {
        if (null === $value || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat('!'.$platform->getDateFormatString(), $value, self::getUtc());
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
