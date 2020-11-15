<?php

namespace eCamp\Lib\Types;

use DateTime;
use DateTimeZone;
use JsonSerializable;

class DateTimeUtc extends DateBase implements JsonSerializable {
    protected string $FORMAT = 'Y-m-d\TH:iP';

    /**
     * DateTimeUTC constructor.
     *
     * @param null|string       $time     Date in string representation
     * @param null|DateTimeZone $timezone Default timezone is UTC
     * @param null|string       $format   Allowed format of the datetime
     *
     * @throws InvalidDateFormatException
     * @throws InvalidZoneOffsetException
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null, string $format = null) {
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        } elseif ('UTC' !== $timezone->getName()) {
            throw new InvalidZoneOffsetException('Invalid zone offset: '.$timezone->getName().'. Should be 0');
        }
        if ('now' !== $time) {
            $this->checkFormat($time, $format);
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
