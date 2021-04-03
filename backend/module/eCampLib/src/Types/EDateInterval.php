<?php

namespace eCamp\Lib\Types;

class EDateInterval extends \DateInterval {
    public static function ofInterval(\DateInterval $dateInterval): EDateInterval {
        $intervalSeconds = self::totalSecondsOf($dateInterval);
        $absIntervalSeconds = abs($intervalSeconds);

        $eDateInterval = new EDateInterval("PT{$absIntervalSeconds}S");
        $eDateInterval->invert = $intervalSeconds < 0 ? 1 : 0;

        return $eDateInterval;
    }

    public static function ofDays(int $days): EDateInterval {
        return new EDateInterval("P{$days}D");
    }

    public static function ofHours(int $hours): EDateInterval {
        return new EDateInterval("PT{$hours}H");
    }

    public static function totalSecondsOf(\DateInterval $interval): int {
        $dateTime = new \DateTime();
        $dateTimeWithInterval = clone $dateTime;
        $dateTimeWithInterval->add($interval);

        return $dateTimeWithInterval->getTimestamp() - $dateTime->getTimestamp();
    }

    public function getTotalMinutes(): int {
        return $this->getTotalSeconds() / 60;
    }

    protected function getTotalSeconds(): int {
        return self::totalSecondsOf($this);
    }
}
