<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventAccessChecker {
    // Definition of event leader roles per provider. Hitobito users with these roles on an event are allowed
    // to create a corresponding camp in eCamp
    private const array LEADER_ROLE_TYPES = [
        HitobitoProvider::PBSMIDATA->value => [
            'Event::Camp::Role::Leader',
            'Event::Role::Leader',
            'Event::Course::Role::Leader',
        ],
    ];

    // Same as LEADER_ROLE_TYPES but for co-leaders. People with this role should be invited to eCamp when triggering the invite
    // from Hitobito feature
    private const array COLEADER_ROLE_TYPES = [
        HitobitoProvider::PBSMIDATA->value => [
            'Event::Role::Leader',
            'Event::Role::AssistantLeader',
            'Event::Role::Cook',
            'Event::Role::Helper',
            'Event::Role::Treasurer',
            'Event::Role::Speaker',
            'Event::Course::Role::Leader',
            'Event::Course::Role::ClassLeader',
            'Event::Course::Role::Advisor',
            'Event::Course::Role::Helper',
            'Event::Camp::Role::AssistantLeader',
            'Event::Camp::Role::Helper',
            'Event::Camp::Role::LeaderMountainSecurity',
            'Event::Camp::Role::LeaderSnowSecurity',
            'Event::Camp::Role::LeaderWaterSecurity',
            'Event::Camp::Role::Leader',
            'Event::Camp::Role::Abteilungsleitung',
            'Event::Camp::Role::Coach',
            'Event::Camp::Role::AdvisorMountainSecurity',
            'Event::Camp::Role::AdvisorSnowSecurity',
            'Event::Camp::Role::AdvisorWaterSecurity',
        ],
    ];

    /**
     * Returns the role types which allow a Hitobito user to create a corresponding camp in eCamp.
     *
     * @return string[]
     */
    public function getLeaderRoleTypes(HitobitoProvider $provider): array {
        return self::LEADER_ROLE_TYPES[$provider->value] ?? [];
    }

    /**
     * Returns the role types which identify a Hitobito user as a leader or co-leader of an event.
     *
     * @return string[]
     */
    public function getCoLeaderRoleTypes(HitobitoProvider $provider): array {
        return self::COLEADER_ROLE_TYPES[$provider->value] ?? [];
    }

    /**
     * Check whether a user has access to the specified event
     * Throws corresponding exceptions if access is not granted.
     */
    public function checkAccess(HitobitoProvider $provider, ClientInterface $client, string $eventId): void {
        $participations = $client->getEventParticipations($eventId);

        if ([] === $participations) {
            throw new NotFoundHttpException("Event \"{$eventId}\" not found");
        }

        $hasAccess = false;
        foreach ($participations as $participation) {
            if ($this->isActiveLeaderParticipation($provider, $participation)) {
                $hasAccess = true;

                break;
            }
        }

        if (!$hasAccess) {
            throw new AccessDeniedHttpException("No access to event \"{$eventId}\"");
        }
    }

    /**
     * Checks whether the given participation has a leader role for the given provider.
     */
    public function isActiveLeaderParticipation(HitobitoProvider $provider, EventParticipation $participation): bool {
        if (!$participation->active) {
            return false;
        }

        return [] !== array_intersect($participation->roleTypes, $this->getLeaderRoleTypes($provider));
    }
}
