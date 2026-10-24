<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\HitobitoEventParticipant;
use App\Service\Hitobito\ClientProvider;
use App\Service\Hitobito\EventAccessChecker;
use App\Service\Hitobito\EventParticipant;
use App\Service\Hitobito\HitobitoProvider;

/**
 * @template-implements ProviderInterface<HitobitoEventParticipant>
 */
class HitobitoEventParticipantProvider implements ProviderInterface {
    public function __construct(
        private readonly ClientProvider $clientProvider,
        private readonly EventAccessChecker $eventAccessChecker,
    ) {}

    /**
     * @return HitobitoEventParticipant[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array {
        $provider = HitobitoProvider::parse($uriVariables['provider']);
        $eventId = $uriVariables['eventId'];

        $client = $this->clientProvider->getClientForCurrentUser($provider);

        $this->eventAccessChecker->checkAccess($provider, $client, $eventId);

        $participants = $client->getEventParticipants($eventId, $this->eventAccessChecker->getCoLeaderRoleTypes($provider));

        return array_map(
            static fn (EventParticipant $participant) => new HitobitoEventParticipant(
                $participant->firstName,
                $participant->lastName,
                $participant->nickname,
                $participant->email,
            ),
            $participants
        );
    }
}
