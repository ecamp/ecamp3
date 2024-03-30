<?php

namespace App\Security\Voter;

use App\Entity\BelongsToCampInterface;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Entity\Camp;
use App\HttpCache\ResponseTagger;
use App\Util\GetCampFromContentNodeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,BelongsToCampInterface | BelongsToContentNodeTreeInterface>
 */
class CampIsPrototypeVoter extends Voter {
    use GetCampFromContentNodeTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ResponseTagger $responseTagger
    ) {}

    protected function supports($attribute, $subject): bool {
        return 'CAMP_IS_PROTOTYPE' === $attribute
        && ($subject instanceof BelongsToCampInterface || $subject instanceof BelongsToContentNodeTreeInterface);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $camp = $this->getCampFromInterface($subject, $this->em);

        if (null === $camp) {
            // Allow access when camp is null.
            // In write operations, this should be handled by validation.
            // Therefore, in read operations this should never happen.
            return true;
        }

        if ($camp->isPrototype) {
            $this->responseTagger->addTags([$camp->getId()]);

            return true;
        }

        return false;
    }
}
