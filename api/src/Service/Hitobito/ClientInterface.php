<?php

namespace App\Service\Hitobito;

interface ClientInterface {
    /**
     * Returns the upcoming events according to the following criteria:
     * - Events are currently running or in the future
     * - Authenticated user is an active participant
     * - User has one of the provided roles on the event participation
     *
     * Only id and name of an event are returned.
     *
     * @param string[] $roleTypes
     *
     * @return Event[]
     */
    public function getUpcomingEvents(array $roleTypes): array;

    /**
     * For a specified event, returns all event participations of the current authenticated user.
     *
     * @return EventParticipation[]
     */
    public function getEventParticipations(string $eventId): array;

    /**
     * Returns all people (first name, last name, nickname, email) that are leaders or co-leaders of the specified event.
     *
     * @param string[] $roleTypes
     *
     * @return EventParticipant[]
     */
    public function getEventParticipants(string $eventId, array $roleTypes): array;

    /**
     * Returns the details of an event by its id.
     */
    public function getEvent(string $eventId): ?Event;
}
