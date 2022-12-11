<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class CampCollaborationResendInvitationProcessor extends AbstractPersistProcessor {
    use CampCollaborationSendEmailTrait;

    public function __construct(
        ProcessorInterface $decorated,
        private Security $security,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private MailService $mailService,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param CampCollaboration $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    /**
     * @param CampCollaboration $data
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->sendInviteEmail($data);
    }
}
