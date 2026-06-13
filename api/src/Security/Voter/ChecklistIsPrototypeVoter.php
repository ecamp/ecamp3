<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,Checklist|ChecklistItem>
 */
class ChecklistIsPrototypeVoter extends Voter {
    #[\Override]
    protected function supports($attribute, $subject): bool {
        return 'CHECKLIST_IS_PROTOTYPE' === $attribute
        && ($subject instanceof Checklist || $subject instanceof ChecklistItem);
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool {
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
