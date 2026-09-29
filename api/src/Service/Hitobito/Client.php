<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client implements ClientInterface {
    // Requested page size when fetching participants
    private const int PARTICIPANTS_PAGE_SIZE = 20;

    // Maximum number of fetched pages when fetching participants (used to avoid eCamp request timeout)
    private const int PARTICIPANTS_MAX_PAGES = 10;

    private readonly HttpClientInterface $httpClient;
    private readonly int $hitobitoUserId;

    public function __construct(
        HttpClientInterface $client,
        string $baseUrl,
        AuthContext $authContext
    ) {
        $this->httpClient = $client->withOptions([
            // add a trailing slash, if not given (if no trailing slash is present in base uri, its path will be overwritten upon any request)
            'base_uri' => rtrim($baseUrl, '/').'/',
            'auth_bearer' => $authContext->getAccessToken(),
        ]);
        // the access token belongs to this user, so both always describe the same Hitobito user
        $this->hitobitoUserId = $authContext->getUserId();
    }

    public function getUpcomingEvents(array $roleTypes): array {
        if ([] === $roleTypes) {
            return [];
        }

        // Fetch events from Hitobito. Note that filtering participations and roles does not exclude the event entirely,
        // it only excludes the participations resp. roles from being listed in its relationships. The events the user
        // is actually a leader of are derived from the included relationships in extractEvents.
        $response = $this->httpClient->request('GET', 'events', [
            'query' => [
                // Include the participations of the current user, along with their roles
                'include' => 'participations.roles',
                'filter' => [
                    // Only include currently running events or events in the future
                    'after_or_on' => ['eq' => (new \DateTimeImmutable('today'))->format('Y-m-d')],
                    // Only include participation relationships for the current authenticated user
                    'participations.participant_id' => ['eq' => $this->hitobitoUserId],
                    // Only include participation relationships of the type person
                    'participations.participant_type' => ['eq' => 'Person'],
                    // Only include roles which allow the user to create a camp from the event
                    'participations.roles.type' => ['eq' => implode(',', $roleTypes)],
                ],
                'fields' => [
                    'events' => 'name',
                    'event_participations' => 'event_id,active',
                    'event_roles' => 'participation_id',
                ],
                // Maximum number of events returned. Since Hitobito does not exclude events without a matching
                // participation (see above) this number needs to be large enough so that enough relevant events are
                // included. Multiple paginated requests are possible, however the eCamp request timeout must not be exceeded.
                'page' => ['size' => 100],
            ],
        ])->toArray();

        return $this->extractEvents($response);
    }

    /**
     * @return EventParticipation[]
     */
    public function getEventParticipations(string $eventId): array {
        $response = $this->httpClient->request('GET', 'event_participations', [
            'query' => [
                'include' => 'roles',
                'filter' => [
                    'participant_id' => ['eq' => $this->hitobitoUserId],
                    'event_id' => ['eq' => $eventId],
                ],
                'fields' => [
                    'event_participations' => 'active',
                    'event_roles' => 'type',
                ],
            ],
        ])->toArray();

        // Extract role types
        $roleTypesById = [];
        foreach ($response['included'] ?? [] as $included) {
            if ('event_roles' === $included['type']) {
                $roleTypesById[$included['id']] = $included['attributes']['type'];
            }
        }

        // Add event participation containing id, active (true/false), the event id and the roles of this user
        $participations = [];
        foreach ($response['data'] as $participation) {
            $roleTypes = [];
            foreach ($participation['relationships']['roles']['data'] ?? [] as $role) {
                if (isset($roleTypesById[$role['id']])) {
                    $roleTypes[] = $roleTypesById[$role['id']];
                }
            }

            $participations[] = new EventParticipation(
                $participation['id'],
                $participation['attributes']['active'],
                $eventId,
                $roleTypes,
            );
        }

        return $participations;
    }

    /**
     * @return EventParticipant[]
     */
    public function getEventParticipants(string $eventId, array $roleTypes): array {
        if ([] === $roleTypes) {
            return [];
        }

        $participants = [];
        $page = 1;

        do {
            $response = $this->httpClient->request('GET', 'event_participations', [
                'query' => [
                    // Include the role entities as well as the participating person
                    'include' => 'roles,participant',
                    'filter' => [
                        'event_id' => ['eq' => $eventId],
                    ],
                    'fields' => [
                        'event_participations' => 'active',
                        'people' => 'first_name,last_name,nickname,email',
                    ],
                    'page' => ['number' => $page, 'size' => self::PARTICIPANTS_PAGE_SIZE],
                ],
            ])->toArray();

            array_push($participants, ...$this->extractEventParticipants($response, $roleTypes));

            ++$page;
        } while (isset($response['links']['next']) && $page <= self::PARTICIPANTS_MAX_PAGES);

        return $participants;
    }

    public function getEvent(string $eventId): ?Event {
        try {
            $response = $this->httpClient->request('GET', "events/{$eventId}", [
                'query' => ['include' => 'dates'],
            ])->toArray();
        } catch (ClientExceptionInterface $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                return null;
            }

            throw $e;
        }

        // Map all included date objects
        $dates = [];
        foreach ($response['included'] ?? [] as $included) {
            if ('dates' === $included['type']) {
                $dates[] = new EventDate(
                    $included['id'],
                    $included['attributes']['label'],
                    $included['attributes']['start_at'],
                    $included['attributes']['finish_at'],
                );
            }
        }

        return new Event(
            $response['data']['id'],
            $response['data']['attributes']['name'],
            $response['data']['attributes']['motto'],
            $response['data']['attributes']['location'],
            $dates,
        );
    }

    /**
     * Extracts the people of all active participations holding one of the given roles from a single page
     * of the /event_participations response.
     *
     * @param string[] $roleTypes
     *
     * @return EventParticipant[]
     */
    private function extractEventParticipants(array $response, array $roleTypes): array {
        // Gather the role types and the people included in this page
        $roleTypesById = [];
        $peopleById = [];
        foreach ($response['included'] ?? [] as $included) {
            if ('event_roles' === $included['type']) {
                $roleTypesById[$included['id']] = $included['attributes']['type'];
            } elseif ('people' === $included['type']) {
                $peopleById[$included['id']] = $included['attributes'];
            }
        }

        $participants = [];
        foreach ($response['data'] as $participation) {
            // Discard inactive participations
            if (!$participation['attributes']['active']) {
                continue;
            }

            // Discard participations without any of the given roles
            $participationRoleTypes = [];
            foreach ($participation['relationships']['roles']['data'] ?? [] as $role) {
                if (isset($roleTypesById[$role['id']])) {
                    $participationRoleTypes[] = $roleTypesById[$role['id']];
                }
            }
            if ([] === array_intersect($participationRoleTypes, $roleTypes)) {
                continue;
            }

            // Discard participations whose person is not included in the relationships
            $personId = $participation['relationships']['participant']['data']['id'] ?? null;
            if (null === $personId || !isset($peopleById[$personId])) {
                continue;
            }

            // Discard people without an email address
            $person = $peopleById[$personId];
            $email = $person['email'] ?? null;
            if (null === $email || '' === $email) {
                continue;
            }

            $participants[] = new EventParticipant(
                $person['first_name'] ?? null,
                $person['last_name'] ?? null,
                $person['nickname'] ?? null,
                $email,
            );
        }

        return $participants;
    }

    /**
     * Extracts relevant events (where the user is leader) from the /events response by checking included
     * participant relationships.
     *
     * @return Event[]
     */
    private function extractEvents(array $response): array {
        // Gather the included participations and the ids of those holding one of the requested roles
        $participationIdsWithRole = [];
        $participations = [];
        foreach ($response['included'] ?? [] as $included) {
            if ('event_roles' === $included['type']) {
                $participationIdsWithRole[$included['attributes']['participation_id']] = true;
            } elseif ('event_participations' === $included['type']) {
                $participations[] = $included;
            }
        }

        // Determine the events the current user actively participates in with one of the given roles
        $eventIdsWithRole = [];
        foreach ($participations as $participation) {
            if (!$participation['attributes']['active'] || !isset($participationIdsWithRole[$participation['id']])) {
                continue;
            }

            $eventIdsWithRole[$participation['attributes']['event_id']] = true;
        }

        // Extract event data
        $events = [];
        foreach ($response['data'] as $event) {
            if (!isset($eventIdsWithRole[$event['id']])) {
                continue;
            }

            $events[] = new Event($event['id'], $event['attributes']['name'], null, null, []);
        }

        return $events;
    }
}
