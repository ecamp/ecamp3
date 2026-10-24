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
class CommentUpdateProcessor extends AbstractPersistProcessor {
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
        if (true === $data->resolved && null === $data->resolvedAt) {
            /** @var User $user */
            $user = $this->security->getUser();

            $data->resolvedAt = new \DateTime();
            $data->resolvedBy = $this->campCollaborationRepository->findByUserAndCamp($user, $data->getCamp());
        }

        if (false === $data->resolved) {
            $data->resolvedAt = null;
            $data->resolvedBy = null;
        }

        return $data;
    }
}
