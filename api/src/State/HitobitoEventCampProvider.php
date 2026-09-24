<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\HitobitoEventCamp;
use App\Entity\Camp;
use App\Exception\HitobitoEventCampException;
use App\Exception\HitobitoEventCampExceptionType;
use App\Repository\CampRepository;
use App\Service\Hitobito\ClientProvider;
use App\Service\Hitobito\EventAccessChecker;
use App\Service\Hitobito\HitobitoProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template-implements ProviderInterface<HitobitoEventCamp>
 */
class HitobitoEventCampProvider implements ProviderInterface {
    public function __construct(
        private readonly Security $security,
        private readonly CampRepository $campRepository,
        private readonly EventAccessChecker $eventAccessChecker,
        private readonly ClientProvider $clientProvider,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?HitobitoEventCamp {
        $provider = HitobitoProvider::parse($uriVariables['provider']);
        $eventId = $uriVariables['eventId'];

        $camp = $this->findCamp($provider, $eventId);

        if (null === $camp) {
            // No camp exists yet, check if camp can be imported by the current user
            $this->checkEventIsImportable($provider, $eventId);

            // Event exists at Hitobito and can be imported by the user
            throw new HitobitoEventCampException(HitobitoEventCampExceptionType::CAMP_NOT_FOUND);
        }

        // Camp exists for the specified event, verify that the user has access
        if (!$this->security->isGranted('CAMP_COLLABORATOR', $camp)) {
            throw new HitobitoEventCampException(HitobitoEventCampExceptionType::CAMP_FORBIDDEN);
        }

        // Return the deep-link camp entity
        return new HitobitoEventCamp($provider->value, $eventId, $camp);
    }

    private function findCamp(HitobitoProvider $provider, string $eventId): ?Camp {
        return $this->campRepository->findOneBy([
            'hitobitoProvider' => $provider,
            'hitobitoEventId' => $eventId,
        ]);
    }

    private function checkEventIsImportable(HitobitoProvider $provider, string $eventId): void {
        $client = $this->clientProvider->getClientForCurrentUser($provider);

        try {
            $this->eventAccessChecker->checkAccess($provider, $client, $eventId);
        } catch (AccessDeniedHttpException) {
            // Event exists at Hitobito, but user doesn't have sufficient permissions
            throw new HitobitoEventCampException(HitobitoEventCampExceptionType::EVENT_FORBIDDEN);
        } catch (NotFoundHttpException) {
            // Event does not exist at Hitobito
            throw new HitobitoEventCampException(HitobitoEventCampExceptionType::EVENT_NOT_FOUND);
        }
    }
}
