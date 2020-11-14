<?php

namespace eCamp\Core\Types;

use DateTime;
use DateTimeZone;

class DateUTC extends DateTime {
    protected string $FORMAT = 'Y-m-d';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string $time
     *
     * @throws \Exception
     */
    public function __construct($time = 'today', DateTimeZone $timezone = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        }
        if ('today' !== $time) {
            $parsedDate = (object) date_parse_from_format($this->FORMAT, $time);
            if ($parsedDate->error_count > 0) {
                throw new \Exception('Invalid date format: '.$time.'. Should be '.$this->FORMAT);
            }
        }
        parent::__construct($time, $timezone);
    }

    public function __toString() {
        return $this->format($this->FORMAT);
    }
}
