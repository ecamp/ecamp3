<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\Comment;
use App\State\Util\AbstractRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends AbstractRemoveProcessor<Activity>
 */
class ActivityRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Activity $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510
        $this->em->remove($data->rootContentNode);

        /** @var Comment[] $comments */
        $comments = $data->comments;
        foreach ($comments as $comment) {
            $comment->orphanDescription = $comment->activity->title;
            $comment->activity->removeComment($comment);
        }
    }
}
