<?php

namespace App\Security\Voter;

use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,Checklist|ChecklistItem>
 */
class ChecklistIsPrototypeVoter extends Voter {
    public function __construct() {}

    protected function supports($attribute, $subject): bool {
        return 'CHECKLIST_IS_PROTOTYPE' === $attribute
        && ($subject instanceof Checklist || $subject instanceof ChecklistItem);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        if ($subject instanceof Checklist) {
            $checklist = $subject;
        }

        if ($subject instanceof ChecklistItem) {
            $checklist = $subject->checklist;
        }

        if (!$checklist) {
            return false;
        }

        return $checklist->isPrototype;
    }
}
