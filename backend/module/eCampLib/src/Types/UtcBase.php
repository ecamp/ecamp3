<?php

namespace eCamp\Lib\Types;

use DateTimeZone;
use JsonSerializable;

abstract class UtcBase extends \DateTime implements JsonSerializable {
    protected string $FORMAT;

    /**
     * UtcBase constructor.
     *
     * @param null|string       $time     Date in string representation
     * @param null|DateTimeZone $timezone Default timezone is UTC
     * @param null|string       $format   Allowed format of the date
     *
     * @throws InvalidDateFormatException
     * @throws InvalidZoneOffsetException
     */
    public function __construct($time = null, DateTimeZone $timezone = null, string $format = null) {
        if (null === $time) {
            $time = $this->getDefaultTimeString();
        } else {
            $format = $format ?? $this->FORMAT;

            $parsedDate = (object) date_parse_from_format($format, $time);
            if ($parsedDate->error_count > 0) {
                throw new InvalidDateFormatException('Invalid date format: '.$time.'. Should be '.$format);
            }
            if (isset($parsedDate->zone) && 0 !== $parsedDate->zone) {
                throw new InvalidZoneOffsetException('Invalid zone offset: '.$parsedDate->zone.'. Should be 0');
            }
        }
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        } elseif ('UTC' !== $timezone->getName()) {
            throw new InvalidZoneOffsetException('Invalid zone offset: '.$timezone->getName().'. Should be 0');
        }
        parent::__construct($time, $timezone);
    }

    public function __toString() {
        return $this->format($this->FORMAT);
    }

    /**
     * @return string with default relative datetime:
     *                - "now" (this exact moment in the given timezone)
     *                - "today" (this day at 00:00:00 in the given timezone)
     */
    abstract public function getDefaultTimeString(): string;

    /**
     * formatter used with json_encode.
     */
    public function jsonSerialize() {
        return $this->__toString();
    }
}
