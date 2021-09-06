<?php

namespace App\Security\Voter;

use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CampRoleVoter extends Voter {
    public const RULE_MAPPING = [
        'CAMP_GUEST' => [CampCollaboration::ROLE_GUEST],
        'CAMP_MEMBER' => [CampCollaboration::ROLE_MEMBER],
        'CAMP_MANAGER' => [CampCollaboration::ROLE_MANAGER],
        'CAMP_COLLABORATOR' => CampCollaboration::VALID_ROLES,
    ];

    protected function supports($attribute, $subject): bool {
        return in_array($attribute, array_keys(self::RULE_MAPPING))
            && ($subject instanceof BelongsToCampInterface || null === $subject)
            && ($subject?->getCamp() instanceof Camp || null === $subject?->getCamp());
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var null|Camp $camp */
        $camp = $subject?->getCamp();
        if (null === $camp) {
            // Allow access when camp is null.
            // In write operations, this should be handled by validation.
            // Therefore, in read operations this should never happen.
            return true;
        }

        return $camp->collaborations->exists(function ($idx, CampCollaboration $collaboration) use ($user, $attribute) {
            return CampCollaboration::STATUS_ESTABLISHED === $collaboration->status
                && $collaboration->user->getId() === $user->getId()
                && in_array($collaboration->role, self::RULE_MAPPING[$attribute], true);
        });
    }
}
