<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\State\Util\AbstractPersistProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @template-extends AbstractPersistProcessor<User>
 */
class UserActivateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CampCollaborationRepository $campCollaborationRepository,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        if ($data->activationKeyHash === md5($data->activationKey)) {
            $data->state = User::STATE_ACTIVATED;
            $data->activationKey = null;
            $data->activationKeyHash = null;
        } else {
            throw new HttpException(422, 'Invalid ActivationKey');
        }

        return $data;
    }

    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var User $user */
        $user = $data;
        $personalInvitationsForNewEmail = $this->campCollaborationRepository->findAllByInviteEmailAndInvited($user->getProfile()->email);
        foreach ($personalInvitationsForNewEmail as $invitation) {
            // Convert all invitations who specifically invited this email address to
            // personal invitations, which the invited user will be able to see and
            // accept / reject in the UI, even without receiving the invitation email.
            // This is done by setting the user field instead of the inviteEmail field.
            $invitation->inviteEmail = null;
            $invitation->user = $user;
            $this->em->persist($invitation);
        }
        $this->em->flush();
    }
}
