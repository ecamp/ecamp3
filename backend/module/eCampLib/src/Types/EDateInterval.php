<?php

namespace eCamp\Lib\Types;

class EDateInterval extends \DateInterval {
    /**
     * DateTime to calculate the intervals always from the same moment.
     */
    const FIXED_DATE_TIME = '2021-01-01 00:00:00Z';

    public static function ofInterval(\DateInterval $dateInterval): EDateInterval {
        $intervalSeconds = self::totalSecondsOf($dateInterval);
        $absIntervalSeconds = abs($intervalSeconds);

        $eDateInterval = new EDateInterval("PT{$absIntervalSeconds}S");
        $eDateInterval->invert = $intervalSeconds < 0 ? 1 : 0;

        return $eDateInterval;
    }

    public static function ofDays(int $days): EDateInterval {
        $absDays = abs($days);
        $dateInterval = new EDateInterval("P{$absDays}D");
        $dateInterval->invert = $days < 0 ? 1 : 0;

        return $dateInterval;
    }

    public static function ofHours(int $hours): EDateInterval {
        $absHours = abs($hours);
        $dateInterval = new EDateInterval("PT{$absHours}H");
        $dateInterval->invert = $hours < 0 ? 1 : 0;

        return $dateInterval;
    }

    public static function totalSecondsOf(\DateInterval $interval): int {
        $dateTime = new \DateTime(self::FIXED_DATE_TIME);
        $dateTimeWithInterval = clone $dateTime;
        $dateTimeWithInterval->add($interval);

        return $dateTimeWithInterval->getTimestamp() - $dateTime->getTimestamp();
    }

    public function getTotalMinutes(): int {
        return $this->getTotalSeconds() / 60;
    }

    public function getTotalSeconds(): int {
        return self::totalSecondsOf($this);
    }

    public function format($format): string {
        $dateTime = new \DateTime(self::FIXED_DATE_TIME);
        $dateTimeWithInterval = clone $dateTime;
        $dateTimeWithInterval->add($this);
        $intervalForFormat = $dateTime->diff($dateTimeWithInterval);

        return $intervalForFormat->format($format);
    }
}
