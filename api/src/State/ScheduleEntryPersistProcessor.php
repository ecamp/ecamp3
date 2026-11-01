<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractPersistProcessor;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractPersistProcessor<ScheduleEntry>
 */
class ScheduleEntryPersistProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param ScheduleEntry $data
     */
    #[\Override]
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var null|ScheduleEntry $before */
        $before = $context['previous_data'] ?? null;

        $this->cacheManager->invalidateTags(array_map(
            fn (Period $period) => CacheTag::forCustomTag($period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG),
            array_values(array_filter([$data->period, $before?->period]))
        ));
    }
}
