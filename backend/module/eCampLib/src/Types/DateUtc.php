<?php

namespace eCamp\Lib\Types;

use DateTimeZone;

class DateUtc extends DateBase {
    protected string $FORMAT = 'Y-m-d';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string       $time     Date in string representation
     * @param null|DateTimeZone $timezone Default timezone is UTC
     * @param null|string       $format   Allowed format of the date
     *
     * @throws InvalidDateFormatException
     * @throws InvalidZoneOffsetException
     */
    public function __construct($time = 'today', DateTimeZone $timezone = null, string $format = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        } elseif ('UTC' !== $timezone->getName()) {
            throw new InvalidZoneOffsetException('Invalid zone offset: '.$timezone->getName().'. Should be 0');
        }
        if ('today' !== $time) {
            $this->checkFormat($time, $format);
        }
        parent::__construct($time, $timezone);
    }
}
