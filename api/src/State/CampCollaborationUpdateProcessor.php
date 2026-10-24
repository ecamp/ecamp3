<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\State\Util\PropertyChangeListener;
use App\Util\IdGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @template-extends AbstractPersistProcessor<CampCollaboration>
 */
class CampCollaborationUpdateProcessor extends AbstractPersistProcessor {
    use CampCollaborationSendEmailTrait;

    public function __construct(
        ProcessorInterface $decorated,
        private Security $security,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private MailService $mailService,
    ) {
        $statusChangeListener = PropertyChangeListener::of(
            extractProperty: fn (CampCollaboration $data) => $data->status,
            beforeAction: $this->onBeforeStatusChange(...),
            afterAction: $this->onAfterStatusChange(...)
        );

        $roleChangeListener = PropertyChangeListener::of(
            extractProperty: fn (CampCollaboration $data) => $data->role,
            beforeAction: $this->onBeforeRoleChange(...),
        );

        parent::__construct(
            $decorated,
            propertyChangeListeners: [
                $statusChangeListener,
                $roleChangeListener,
            ]
        );
    }

    public function onBeforeStatusChange(CampCollaboration $data): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    public function onBeforeRoleChange(CampCollaboration $data, CampCollaboration $previous): CampCollaboration {
        if (null === $data->user) {
            return $data;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        // If the update does not affect the own collaboration, the voter works.
        if ($data->user->getId() !== $user->getId()) {
            return $data;
        }
        if (in_array($previous->role, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER], true)) {
            return $data;
        }

        throw new HttpException(403, 'Not authorized to change role');
    }

    public function onAfterStatusChange(CampCollaboration $data): void {
        $this->sendInviteEmail($data);
    }
}
