<?php

namespace eCamp\Lib\Types;

class EDateInterval extends \DateInterval {
    public static function ofDays(int $days): EDateInterval {
        return new EDateInterval("P{$days}D");
    }

    public static function ofHours(int $hours): EDateInterval {
        return new EDateInterval("PT{$hours}H");
    }

    public function getTotalMinutes(): int {
        $dateTime = new \DateTime();
        $dateTimeWithInterval = clone $dateTime;
        $dateTimeWithInterval->add($this);

        return ($dateTimeWithInterval->getTimestamp() - $dateTime->getTimestamp()) / 60;
    }
}
