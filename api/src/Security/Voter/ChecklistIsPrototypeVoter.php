<?php

namespace App\Security\Voter;

use App\Entity\Checklist;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,Checklist>
 */
class ChecklistIsPrototypeVoter extends Voter {
    public function __construct() {}

    protected function supports($attribute, $subject): bool {
        return 'CHECKLIST_IS_PROTOTYPE' === $attribute && $subject instanceof Checklist;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        /** @var Checklist $checklist */
        $checklist = $subject;

        return $checklist->isPrototype;
    }
}
