<?php

namespace eCamp\LibTest\Types;

use eCamp\Lib\Types\EDateInterval;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class EDateIntervalTest extends TestCase {
    public function testCloneDateInterval() {
        $dateInterval = new \DateInterval('P1D');
        $eDateInterval = EDateInterval::ofInterval($dateInterval);

        self::assertThat(totalSecondsOf($dateInterval), self::equalTo(totalSecondsOf($eDateInterval)));

        $dateInterval = new \DateInterval('P1D');
        $dateInterval->invert = 1;
        $eDateInterval = EDateInterval::ofInterval($dateInterval);

        self::assertThat(totalSecondsOf($dateInterval), self::equalTo(totalSecondsOf($eDateInterval)));
    }

    public function testFormatIsTheSameExceptTheTotalDays() {
        $dateInterval = new \DateInterval('P2Y4M24DT2H5M3S');
        $eDateInterval = EDateInterval::ofInterval($dateInterval);
        $format = 'sign: %R years: %Y months: %M days: %D hours: %H minutes: %I seconds: %s microseconds: %F';

        self::assertThat($eDateInterval->format($format), self::equalTo($dateInterval->format($format)));

        $format = 'total days: %a';
        self::assertThat($eDateInterval->format($format), self::logicalNot(self::equalTo($dateInterval->format($format))));
    }

    public function testCalculateTotalSeconds() {
        $hours = 5;
        self::assertThat(EDateInterval::ofHours($hours)->getTotalSeconds(), self::equalTo($hours * 60 * 60));
        $days = 540;
        self::assertThat(EDateInterval::ofDays($days)->getTotalSeconds(), self::equalTo($days * 24 * 60 * 60));
        $hours = -5;
        self::assertThat(EDateInterval::ofHours($hours)->getTotalSeconds(), self::equalTo($hours * 60 * 60));
        $days = -540;
        self::assertThat(EDateInterval::ofDays($days)->getTotalSeconds(), self::equalTo($days * 24 * 60 * 60));
    }

    public function testCalculateTotalMinutes() {
        $hours = 5;
        self::assertThat(EDateInterval::ofHours($hours)->getTotalMinutes(), self::equalTo($hours * 60));
        $days = 540;
        self::assertThat(EDateInterval::ofDays($days)->getTotalMinutes(), self::equalTo($days * 24 * 60));
        $hours = -5;
        self::assertThat(EDateInterval::ofHours($hours)->getTotalMinutes(), self::equalTo($hours * 60));
        $days = -540;
        self::assertThat(EDateInterval::ofDays($days)->getTotalMinutes(), self::equalTo($days * 24 * 60));
    }
}

function totalSecondsOf(\DateInterval $dateInterval): int {
    return EDateInterval::totalSecondsOf($dateInterval);
}
