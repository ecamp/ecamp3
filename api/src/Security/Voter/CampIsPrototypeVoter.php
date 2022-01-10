<?php

namespace App\Security\Voter;

use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CampIsPrototypeVoter extends Voter {
    protected function supports($attribute, $subject): bool {
        return 'CAMP_IS_PROTOTYPE' === $attribute
            && ($subject instanceof BelongsToCampInterface);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        /** @var null|Camp $camp */
        $camp = $subject?->getCamp();
        if (null === $camp) {
            // Allow access when camp is null.
            // In write operations, this should be handled by validation.
            // Therefore, in read operations this should never happen.
            return true;
        }

        return $camp->isPrototype;
    }
}
