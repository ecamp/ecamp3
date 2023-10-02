<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Doctrine\ORM\EntityManagerInterface;
use App\Util\GetCampFromContentNodeTrait;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Entity\BelongsToCampInterface;
use ApiPlatform\Api\IriConverterInterface;

/**
 * @extends Voter<string,mixed>
 */
class CampIsPrototypeVoter extends Voter {
    use GetCampFromContentNodeTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private SymfonyResponseTagger $responseTagger,
        private IriConverterInterface $iriConverter,
    ) {
    }

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
