<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CampCollaborationDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(
        private ContextAwareDataPersisterInterface $dataPersister,
        private Security $security,
        private UserRepository $userRepository,
        private MailService $mailService,
        private RequestStack $requestStack,
    ) {
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof CampCollaboration) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param CampCollaboration $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        /** @var User $user */
        $user = $this->security->getUser();
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            //TODO: check if the campCollaboration already exists.
            if (CampCollaboration::STATUS_INVITED == $data->status && $data->inviteEmail) {
                $userByInviteEmail = $this->userRepository->findOneBy(['email' => $data->inviteEmail]);
                if (null != $userByInviteEmail) {
                    $data->user = $userByInviteEmail;
                }
                $data->inviteKey = IdGenerator::generateRandomHexString(64);
                $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->inviteEmail);
            }
        } elseif ('patch' === ($context['item_operation_name'] ?? null)) {
            /** @var CampCollaboration $campCollaborationBefore */
            $campCollaborationBefore = $this->requestStack->getCurrentRequest()->attributes->get('previous_data');

            switch ($campCollaborationBefore->status) {
                case CampCollaboration::STATUS_INACTIVE:
                    if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
                        $campCollaborationUser = $campCollaborationBefore->user;
                        $inviteEmail = $campCollaborationBefore->inviteEmail;
                        if (null != $campCollaborationUser) {
                            $inviteEmail = $campCollaborationUser->email;
                        }
                        $data->inviteKey = IdGenerator::generateRandomHexString(64);
                        $this->mailService->sendInviteToCampMail(
                            $user,
                            $campCollaborationBefore->camp,
                            $data->inviteKey,
                            $inviteEmail
                        );
                    }

                    break;
            }
        } elseif (CampCollaboration::RESEND_INVITATION === ($context['item_operation_name'] ?? null)) {
            $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->inviteEmail);
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
