<?php

namespace App\Types;

use DateTimeZone;
use JsonSerializable;

class Date extends \DateTime implements JsonSerializable {
    protected string $FORMAT = 'Y-m-d';

    /**
     * @param null|string       $time     Date in string representation
     * @param null|DateTimeZone $timezone Default timezone is UTC
     *
     * @throws \Exception
     */
    public function __construct($time = null, DateTimeZone $timezone = null) {
        if (null === $time) {
            $time = 'today';
        } else {
            $parsedDate = (object) date_parse_from_format($this->FORMAT, $time);
            if ($parsedDate->error_count > 0) {
                throw new \RuntimeException('Invalid date format: '.$time.'. Should be '.$this->FORMAT);
            }
            if (isset($parsedDate->zone) && 0 !== $parsedDate->zone) {
                throw new \RuntimeException('Invalid zone offset: '.$parsedDate->zone.'. Should be 0');
            }
        }
        if (null === $timezone) {
            $timezone = new DateTimeZone('UTC');
        } elseif ('UTC' !== $timezone->getName()) {
            throw new \RuntimeException('Invalid zone offset: '.$timezone->getName().'. Should be 0');
        }
        parent::__construct($time, $timezone);
    }

    public function __toString(): string {
        return $this->format($this->FORMAT);
    }

    /**
     * formatter used with json_encode.
     */
    public function jsonSerialize(): string {
        return $this->__toString();
    }
}
