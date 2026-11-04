<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractPersistProcessor;
use App\Util\GetPeriodsFromActivityTrait;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractPersistProcessor<Activity>
 */
class ActivityUpdateProcessor extends AbstractPersistProcessor {
    use GetPeriodsFromActivityTrait;

    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Activity $data
     */
    #[\Override]
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var null|Activity $before */
        $before = $context['previous_data'] ?? null;

        if ($before?->category === $data->category) {
            return;
        }

        $this->cacheManager->invalidateTags(array_map(
            fn (Period $period) => CacheTag::forCustomTag($period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG),
            $this->getPeriodsFromActivity($data)
        ));
    }
}
