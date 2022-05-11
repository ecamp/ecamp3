<?php

namespace App\Security\Voter;

use App\Entity\BelongsToCampInterface;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Util\GetCampFromContentNodeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CampRoleVoter extends Voter {
    use GetCampFromContentNodeTrait;

    public const RULE_MAPPING = [
        'CAMP_GUEST' => [CampCollaboration::ROLE_GUEST],
        'CAMP_MEMBER' => [CampCollaboration::ROLE_MEMBER],
        'CAMP_MANAGER' => [CampCollaboration::ROLE_MANAGER],
        'CAMP_COLLABORATOR' => CampCollaboration::VALID_ROLES,
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    protected function supports($attribute, $subject): bool {
        return in_array($attribute, array_keys(self::RULE_MAPPING))
            && ($subject instanceof BelongsToCampInterface || $subject instanceof BelongsToContentNodeTreeInterface);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $camp = $this->getCampFromInterface($subject, $this->em);

        if (null === $camp) {
            // Allow access when camp is null.
            // In write operations, this should be handled by validation.
            // Therefore, in read operations this should never happen.
            return true;
        }

        return $camp->collaborations
            ->filter(self::withStatus(CampCollaboration::STATUS_ESTABLISHED))
            ->filter(self::ofUser($user))
            ->filter(self::withRole($attribute))
            ->exists(fn () => true)
        ;
    }

    private static function withStatus($status) {
        return function (CampCollaboration $collaboration) use ($status) {
            return $status === $collaboration->status;
        };
    }

    private static function ofUser($user) {
        return function (CampCollaboration $collaboration) use ($user) {
            return $collaboration->user->getId() === $user->getId();
        };
    }

    private static function withRole($attribute) {
        return function (CampCollaboration $collaboration) use ($attribute) {
            return in_array($collaboration->role, self::RULE_MAPPING[$attribute], true);
        };
    }
}
