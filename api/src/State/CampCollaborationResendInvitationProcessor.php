<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class CampCollaborationResendInvitationProcessor extends AbstractPersistProcessor {
    public function __construct(
        private ProcessorInterface $decorated,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private MailService $mailService,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param CampCollaboration $data
     */
    public function onBefore($data): CampCollaboration {
        $data->inviteKey = IdGenerator::generateRandomHexString(64);
        $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);

        return $data;
    }

    /**
     * @param CampCollaboration $data
     */
    public function onAfter($data): void {
        $this->mailService->sendInviteToCampMail($data);
    }
}
