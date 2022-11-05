<?php

namespace App\Types\Doctrine;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Replacement for Doctrine's DateTime Type.
 *
 * The original DateTimeType truncates any time zone information (e.g. '1985-09-01 10:10:10+02:00'
 * and '1985-09-01 10:10:10+01:00' are both stored as '1985-09-01 10:10:10'). Dates read from database
 * are interpreted in the local timezone of the server.
 *
 * To avoid using DateTimeTzType (which would also store timezone information), this type class converts
 * all PHP DateTime to UTC before storing to database. All values read from database are interpreted
 * to be in UTC (e.g. '1985-09-01 10:10:10+02:00' becomes '1985-09-01 08:10:10' in the database and
 * '1985-09-01 08:10:10+00:00' if read back from the database)
 */
class UTCDateTimeType extends DateTimeType {
    private static ?\DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            $value = $value->setTimeZone(self::getUtc());

            return parent::convertToDatabaseValue($value, $platform);
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTimeInterface {
        if (null === $value || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::getUtc());

        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $val;
    }

    private static function getUtc(): \DateTimeZone {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }
}
