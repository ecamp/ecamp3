<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Camp;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractRemoveProcessor;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractRemoveProcessor<Period>
 */
class PeriodRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Period $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->cacheManager->invalidateTags([CacheTag::forCustomTag($data->camp, Camp::DAY_NUMBERS_CACHE_TAG)]);
    }
}
