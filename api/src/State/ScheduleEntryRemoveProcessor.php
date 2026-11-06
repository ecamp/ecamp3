<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractRemoveProcessor;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractRemoveProcessor<ScheduleEntry>
 */
class ScheduleEntryRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param ScheduleEntry $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->cacheManager->invalidateTags([CacheTag::forCustomTag($data->period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG)]);
    }
}
