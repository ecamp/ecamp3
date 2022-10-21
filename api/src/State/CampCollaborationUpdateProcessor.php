<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Service\MailService;
use App\State\Util\PropertyChangeListener;
use App\Util\IdGenerator;

class CampCollaborationUpdateProcessor implements ProcessorInterface {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        private ProcessorInterface $decorated,
        private MailService $mailService,
    ) {
        $statusChangeListener = PropertyChangeListener::of(
            extractProperty: fn (CampCollaboration $data) => $data->status,
            beforeAction: fn (CampCollaboration $data) => $this->onBeforeStatusChange($data),
            afterAction: fn (CampCollaboration $data) => $this->onAfterStatusChange($data)
        );
    }

    /**
     * @param CampCollaboration $data
     *
     * @return CampCollaboration
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) {
        /** @var ?CampCollaboration $previousData */
        $previousData = $context['previous_data'];

        if (null === $previousData) {
            return $this->decorated->process($data, $operation, $uriVariables, $context);
        }

        $previousStatus = $previousData->status;
        if ($previousStatus === $data->status) {
            return $this->decorated->process($data, $operation, $uriVariables, $context);
        }

        $data = $this->onBeforeStatusChange($data);

        $data = $this->decorated->process($data, $operation, $uriVariables, $context);

        return $this->onAfterStatusChange($data);
    }

    public function onBeforeStatusChange(CampCollaboration $data): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    public function onAfterStatusChange(CampCollaboration $data): void {
        $this->mailService->sendInviteToCampMail($data);
    }
}
