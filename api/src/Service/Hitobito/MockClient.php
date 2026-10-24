<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

/**
 * MockClient returns mocked Hitobito data used for local development and testing.
 */
class MockClient implements ClientInterface {
    public const string EVENT_ID_LEADER = '123';
    public const string EVENT_ID_COLEADER = '456';
    public const string EVENT_DATE_ID = '789';

    public function getUpcomingEvents(array $roleTypes): array {
        $events = [];
        foreach ($this->getMockParticipations() as $participation) {
            if (!$participation->active || [] === array_intersect($participation->roleTypes, $roleTypes)) {
                continue;
            }

            $event = $this->getEvent($participation->eventId);
            if (null !== $event) {
                $events[] = $event;
            }
        }

        return $events;
    }

    public function getEventParticipations(string $eventId): array {
        return array_values(array_filter(
            $this->getMockParticipations(),
            static fn (EventParticipation $participation) => $eventId === $participation->eventId,
        ));
    }

    public function getEventParticipants(string $eventId, array $roleTypes): array {
        if (self::EVENT_ID_LEADER !== $eventId || [] === $roleTypes) {
            return [];
        }

        return [
            new EventParticipant('Ellen', 'Bloch', 'Quo', 'bloch.ellen@hitobito.example.com'),
            new EventParticipant('Lee', 'Frauen', 'Maiores', 'frauen_lee@hitobito.example.com'),
        ];
    }

    public function getEvent(string $eventId): ?Event {
        return match ($eventId) {
            self::EVENT_ID_LEADER => new Event(
                self::EVENT_ID_LEADER,
                'Testlager',
                'Testmotto',
                'Testort',
                [
                    new EventDate(
                        self::EVENT_DATE_ID,
                        'Hauptlager',
                        '2026-01-01T00:00:00+00:00',
                        '2026-02-01T00:00:00+00:00',
                    ),
                ],
            ),
            self::EVENT_ID_COLEADER => new Event(
                self::EVENT_ID_COLEADER,
                'Testlager 2',
                null,
                null,
                [],
            ),
            default => null,
        };
    }

    /**
     * @return EventParticipation[]
     */
    private function getMockParticipations(): array {
        return [
            new EventParticipation(
                '1',
                true,
                self::EVENT_ID_LEADER,
                ['Event::Camp::Role::Leader'],
            ),
            new EventParticipation(
                '2',
                true,
                self::EVENT_ID_COLEADER,
                ['Event::Role::Helper'],
            ),
        ];
    }
}
