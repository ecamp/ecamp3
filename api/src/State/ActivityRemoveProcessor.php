<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\Period;
use App\HttpCache\CacheTag;
use App\State\Util\AbstractRemoveProcessor;
use App\Util\GetPeriodsFromActivityTrait;
use Doctrine\ORM\EntityManagerInterface;
use FOS\HttpCacheBundle\CacheManager;

/**
 * @template-extends AbstractRemoveProcessor<Activity>
 */
class ActivityRemoveProcessor extends AbstractRemoveProcessor {
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
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->cacheManager->invalidateTags(array_map(
            fn (Period $period) => CacheTag::forCustomTag($period, Period::SCHEDULE_ENTRY_NUMBERS_CACHE_TAG),
            $this->getPeriodsFromActivity($data)
        ));

        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510
        $this->em->remove($data->rootContentNode);

        /** @var Comment[] $comments */
        $comments = $data->comments;
        foreach ($comments as $comment) {
            $comment->orphanDescription = $comment->activity->category->short.' '.$comment->activity->title;
            $comment->activity->removeComment($comment);
        }
    }
}
