<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\State\Util\PropertyChangeListener;
use App\Util\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class CampCollaborationUpdateProcessor extends AbstractPersistProcessor {
    public function __construct(
        private ProcessorInterface $decorated,
        private MailService $mailService,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
        $statusChangeListener = PropertyChangeListener::of(
            extractProperty: fn (CampCollaboration $data) => $data->status,
            beforeAction: fn (CampCollaboration $data) => $this->onBeforeStatusChange($data),
            afterAction: fn (CampCollaboration $data) => $this->onAfterStatusChange($data)
        );

        parent::__construct(
            $decorated,
            propertyChangeListeners: [$statusChangeListener]
        );
    }

    public function onBeforeStatusChange(CampCollaboration $data): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    public function onAfterStatusChange(CampCollaboration $data): void {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            $this->mailService->sendInviteToCampMail($data);
        }
    }
}
