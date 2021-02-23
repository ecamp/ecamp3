<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Service\Invitation;
use Laminas\Hydrator\HydratorInterface;

class InvitationHydrator implements HydratorInterface {
    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var Invitation $invitation */
        $invitation = $object;

        return [
            'campId' => $invitation->getCampId(),
            'campTitle' => $invitation->getCampTitle(),
            'userDisplayName' => $invitation->getUserDisplayName(),
            'userAlreadyInCamp' => $invitation->isUserAlreadyInCamp(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): Invitation {
        throw new \BadMethodCallException('This method is not implemented, because Invitations are read only');
    }
}
