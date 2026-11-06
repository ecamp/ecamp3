<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractPersistProcessor;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractPersistProcessor<Category>
 */
class CategoryUpdateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Category $data
     */
    #[\Override]
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var null|Category $before */
        $before = $context['previous_data'] ?? null;

        if ($before?->numberingStyle === $data->numberingStyle) {
            return;
        }

        $this->cacheManager->invalidateTags(
            $data->camp->periods->map(fn (Period $period) => CacheTag::forCustomTag($period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG))->getValues()
        );
    }
}
