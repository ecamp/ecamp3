<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use App\Util\GetPeriodsFromActivityTrait;
use Doctrine\ORM\EntityManagerInterface;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractPersistProcessor<Activity>
 */
class ActivityCreateProcessor extends AbstractPersistProcessor {
    use GetPeriodsFromActivityTrait;

    public function __construct(
        ProcessorInterface $decorated,
        private readonly EntityManagerInterface $em,
        private readonly CacheManager $cacheManager,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Activity $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Activity {
        // @phpstan-ignore nullsafe.neverNull
        if (!isset($data->category?->rootContentNode)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is null. Object of type '.ColumnLayout::class.' expected.');
        }
        if (!$data->category->rootContentNode instanceof ColumnLayout) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is of wrong type. Object of type '.ColumnLayout::class.' expected.');
        }

        $targetCamp = $data->category->camp;
        $data->camp = $data->category->camp;
        $rootContentNodePrototype = $data->category->rootContentNode;

        if (null !== $data->copyActivitySource) {
            // CopyActivity Source is set -> copy it's content (rootContentNode)
            $rootContentNodePrototype = $data->copyActivitySource->rootContentNode;
        }

        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $data->setRootContentNode($rootContentNode);

        // deep copy from category root node
        $entityMap = new EntityMap($targetCamp);
        $rootContentNode->copyFromPrototype($rootContentNodePrototype, $entityMap);

        return $data;
    }

    /**
     * @param Activity $data
     */
    #[\Override]
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->cacheManager->invalidateTags(array_map(
            fn (Period $period) => CacheTag::forCustomTag($period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG),
            $this->getPeriodsFromActivity($data)
        ));
    }
}
