<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\HitobitoEvent;
use App\DTO\HitobitoEventDate;
use App\Repository\CampRepository;
use App\Service\Hitobito\ClientInterface;
use App\Service\Hitobito\ClientProvider;
use App\Service\Hitobito\Event;
use App\Service\Hitobito\EventAccessChecker;
use App\Service\Hitobito\HitobitoProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template-implements ProviderInterface<HitobitoEvent>
 */
class HitobitoEventProvider implements ProviderInterface {
    public function __construct(
        private readonly ClientProvider $clientProvider,
        private readonly EventAccessChecker $eventAccessChecker,
        private readonly CampRepository $campRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|HitobitoEvent|null {
        $provider = HitobitoProvider::parse($uriVariables['provider']);

        $client = $this->clientProvider->getClientForCurrentUser($provider);

        if (isset($uriVariables['eventId'])) {
            return $this->provideItem($provider, $client, $uriVariables['eventId']);
        }

        return $this->provideCollection($provider, $client);
    }

    /**
     * @return HitobitoEvent[]
     */
    private function provideCollection(HitobitoProvider $provider, ClientInterface $client): array {
        $events = $client->getUpcomingEvents($this->eventAccessChecker->getLeaderRoleTypes($provider));
        if ([] === $events) {
            return [];
        }

        $importedEventIds = $this->findImportedEventIds($provider, array_map(static fn (Event $event) => $event->id, $events));

        return array_map(
            fn (Event $event) => $this->toHitobitoEvent($provider, $event, isset($importedEventIds[$event->id])),
            $events
        );
    }

    private function provideItem(HitobitoProvider $provider, ClientInterface $client, string $eventId): HitobitoEvent {
        $this->eventAccessChecker->checkAccess($provider, $client, $eventId);

        $event = $client->getEvent($eventId);
        if (null === $event) {
            throw new NotFoundHttpException("Event \"{$eventId}\" not found");
        }

        $importedEventIds = $this->findImportedEventIds($provider, [$event->id]);

        return $this->toHitobitoEvent($provider, $event, isset($importedEventIds[$event->id]));
    }

    /**
     * @param string[] $eventIds
     *
     * @return array<string, true>
     */
    private function findImportedEventIds(HitobitoProvider $provider, array $eventIds): array {
        $camps = $this->campRepository->findBy([
            'hitobitoProvider' => $provider,
            'hitobitoEventId' => $eventIds,
        ]);

        $importedEventIds = [];
        foreach ($camps as $camp) {
            $importedEventIds[$camp->hitobitoEventId] = true;
        }

        return $importedEventIds;
    }

    private function toHitobitoEvent(HitobitoProvider $provider, Event $event, bool $isImported): HitobitoEvent {
        $hitobitoEvent = new HitobitoEvent($provider->value, $event->id, $event->name, $event->motto, $event->location);

        $hitobitoEvent->isImported = $isImported;

        $hitobitoEvent->dates = array_map(
            static fn ($date) => new HitobitoEventDate($date->id, $date->label, $date->startAt, $date->finishAt),
            $event->dates
        );

        return $hitobitoEvent;
    }
}
