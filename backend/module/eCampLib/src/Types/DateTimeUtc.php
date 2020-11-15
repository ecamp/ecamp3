<?php

namespace eCamp\Lib\Types;

use DateTime;
use DateTimeZone;

class DateTimeUtc extends DateTime {
    protected string $FORMAT = 'Y-m-dTH:iP';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string $time
     *
     * @throws InvalidFormatException
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null, string $format = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        }
        if ('now' !== $time) {
            $parsedDate = (object) date_parse_from_format($format ? $format : $this->FORMAT, $time);
            if ($parsedDate->error_count > 0) {
                throw new InvalidFormatException('Invalid date format: '.$time.'. Should be '.$format ? $format : $this->FORMAT);
            }
        }
        parent::__construct($time, $timezone);
    }

    public function __toString() {
        return $this->format($this->FORMAT);
    }
}
