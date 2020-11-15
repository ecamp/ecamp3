<?php

namespace eCamp\Lib\Types;

use DateTime;
use DateTimeZone;
use JsonSerializable;

class DateUtc extends DateTime implements JsonSerializable {
    protected string $FORMAT = 'Y-m-d';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string $time
     *
     * @throws InvalidDateFormatException
     */
    public function __construct($time = 'today', DateTimeZone $timezone = null, string $format = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        }
        if ('today' !== $time) {
            $format = $format ?? $this->FORMAT;

            $parsedDate = (object) date_parse_from_format($format, $time);
            if ($parsedDate->error_count > 0) {
                throw new InvalidDateFormatException('Invalid date format: '.$time.'. Should be '.$format);
            }
        }
        parent::__construct($time, $timezone);
    }

    public function __toString() {
        return $this->format($this->FORMAT);
    }

    /**
     * formatter used with json_encode.
     */
    public function jsonSerialize() {
        return $this->__toString();
    }
}
