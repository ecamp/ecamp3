<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class CampCollaborationResendInvitationProcessor implements ProcessorInterface {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        private ProcessorInterface $decorated,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private MailService $mailService,
    ) {
    }

    /**
     * @param CampCollaboration $data
     *
     * @return CampCollaboration
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) {
        $data = $this->onBeforeResendInvitation($data);

        $data = $this->decorated->process($data, $operation, $uriVariables, $context);

        return $this->onAfterResendInvitation($data);
    }

    public function onBeforeResendInvitation(CampCollaboration $data) {
        $data->inviteKey = IdGenerator::generateRandomHexString(64);
        $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);

        return $data;
    }

    public function onAfterResendInvitation(CampCollaboration $data) {
        $this->mailService->sendInviteToCampMail($data);
    }
}
