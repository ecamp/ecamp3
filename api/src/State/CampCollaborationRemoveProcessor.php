<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Repository\CommentRepository;
use App\State\Util\AbstractRemoveProcessor;

/**
 * @template-extends AbstractRemoveProcessor<CampCollaboration>
 */
class CampCollaborationRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CommentRepository $commentRepository,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param CampCollaboration $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        foreach ($this->commentRepository->findBy(['author' => $data]) as $comment) {
            $comment->authorDisplayName = $data->user?->getDisplayName() ?? $data->inviteEmail;
            $comment->author = null;
        }
    }
}
