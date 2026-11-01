<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Camp;
use App\Entity\Day;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractPersistProcessor;
use App\Util\DateTimeUtil;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractPersistProcessor<Period>
 */
class PeriodPersistProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Period $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Period {
        self::moveDaysAndScheduleEntries($data, $context['previous_data'] ?? null);
        self::removeExtraDays($data);
        self::addMissingDays($data);

        return $data;
    }

    /**
     * @param Period $data
     */
    #[\Override]
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var null|Period $before */
        $before = $context['previous_data'] ?? null;

        if ($before && $before->start == $data->start && $before->end == $data->end) {
            return;
        }

        $this->cacheManager->invalidateTags([CacheTag::forCustomTag($data->camp, Camp::DAY_NUMBERS_CACHE_TAG)]);
    }

    public static function moveDaysAndScheduleEntries(Period $period, ?Period $originalPeriod = null) {
        if (!$originalPeriod) {
            return;
        }

        // moveScheduleEntries === true: scheduleEntries move relative to the start date (no change of offset needed -> return)
        // moveScheduleEntries === false: scheduleEntries stay absolutely on the scheduled calendar date (change of offset needed)
        if ($period->moveScheduleEntries) {
            return;
        }

        $deltaMinutes = DateTimeUtil::differenceInMinutes($originalPeriod->start, $period->start);
        if (0 === $deltaMinutes) {
            return;
        }

        // Move ScheduleEntries
        // --> existing scheduleEntries outside the new period boundary are not possible (validation should have already failed)
        foreach ($period->scheduleEntries as $scheduleEntry) {
            $scheduleEntry->startOffset -= $deltaMinutes;
            $scheduleEntry->endOffset -= $deltaMinutes;
        }

        // Move days (incl. related DayResponsibles)
        // --> existing days outside the new period boundary will be removed in `removeExtraDays`
        $deltaDays = intval(floor($deltaMinutes / 60 / 24));
        foreach ($period->getDays() as $day) {
            $day->dayOffset -= $deltaDays;
        }
    }

    public static function removeExtraDays(Period $period) {
        $length = $period->getPeriodLength();
        $days = $period->getDays();

        foreach ($days as $day) {
            if ($day->dayOffset < 0 || $day->dayOffset >= $length) {
                $period->removeDay($day);
            }
        }
    }

    public static function addMissingDays(Period $period) {
        $length = $period->getPeriodLength();

        for ($i = 0; $i < $length; ++$i) {
            $dayAlreadyExists = $period->days->exists(fn ($key, Day $day) => $day->dayOffset === $i);
            if (!$dayAlreadyExists) {
                $day = new Day();
                $day->dayOffset = $i;
                $period->addDay($day);
            }
        }
    }
}
