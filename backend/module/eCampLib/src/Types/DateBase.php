<?php

namespace eCamp\Lib\Types;

abstract class DateBase extends \DateTime {
    protected string $FORMAT;

    public function __toString() {
        return $this->format($this->FORMAT);
    }

    /**
     * formatter used with json_encode.
     */
    public function jsonSerialize() {
        return $this->__toString();
    }

    /**
     * @param null|string $time   Date in string representation
     * @param null|string $format Allowed format of the date
     *
     * @throws InvalidDateFormatException
     * @throws InvalidZoneOffsetException
     */
    protected function checkFormat(?string $time, ?string $format): void {
        $format = $format ?? $this->FORMAT;

        $parsedDate = (object) date_parse_from_format($format, $time);
        if ($parsedDate->error_count > 0) {
            throw new InvalidDateFormatException('Invalid date format: '.$time.'. Should be '.$format);
        }
        if (isset($parsedDate->zone) && 0 !== $parsedDate->zone) {
            throw new InvalidZoneOffsetException('Invalid zone offset: '.$parsedDate->zone.'. Should be 0');
        }
    }
}
