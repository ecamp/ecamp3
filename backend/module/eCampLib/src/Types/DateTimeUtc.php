<?php

namespace eCamp\Core\Types;

use DateTime;
use DateTimeZone;
use eCamp\Lib\Types\InvalidFormatException;

class DateTimeUtc extends DateTime {
    protected string $FORMAT = 'Y-m-dTH:iP';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string $time
     *
     * @param DateTimeZone|null $timezone
     *
     * @throws InvalidFormatException
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        }
        if ('now' !== $time) {
            $parsedDate = (object) date_parse_from_format($this->FORMAT, $time);
            if ($parsedDate->error_count > 0) {
                throw new InvalidFormatException('Invalid date format: '.$time.'. Should be '.$this->FORMAT);
            }
        }
        parent::__construct($time, $timezone);
    }

    public function __toString() {
        return $this->format($this->FORMAT);
    }
}
