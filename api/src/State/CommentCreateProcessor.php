<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @template-extends AbstractPersistProcessor<Comment>
 */
class CommentCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly Security $security,
        private readonly CampCollaborationRepository $campCollaborationRepository,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Comment $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Comment {
        /** @var User $user */
        $user = $this->security->getUser();

        // A reply is about whatever its thread is about
        if (null !== $data->parent) {
            $data->camp = $data->parent->camp;
            foreach (Comment::ANCHOR_FIELDS as $field) {
                $data->{$field} = $data->parent->{$field};
            }
            $data->orphanDescription = $data->parent->orphanDescription;
        }

        $data->author = $this->campCollaborationRepository->findByUserAndCamp($user, $data->camp);

        return $data;
    }
}
