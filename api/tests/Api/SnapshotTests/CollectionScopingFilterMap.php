<?php

declare(strict_types=1);

namespace App\Tests\Api\SnapshotTests;

/**
 * Collections cannot be listed unfiltered, see App\State\RequireCollectionFilterProvider.
 * This maps a collection endpoint to a query string scoping it to the fixtures of camp1, or to
 * an empty string for the few collections which are not camp specific.
 *
 * A newly added collection endpoint lands in the default arm and, if it needs a different filter,
 * fails with a 400. That is intentional: it has to be decided how that collection is scoped.
 */
class CollectionScopingFilterMap {
    public static function get(string $collectionEndpoint, array $fixtures): string {
        $camp = '/camps/'.$fixtures['camp1']->getId();
        $period = '/periods/'.$fixtures['period1']->getId();

        return match ($collectionEndpoint) {
            '/content_types' => '',
            '/camps' => '?campCollaborator=/users/'.$fixtures['user1manager']->getId(),
            '/activity_responsibles' => '?activity.camp='.$camp,
            '/days' => '?period.camp='.$camp,
            '/day_responsibles' => '?day.period='.$period,
            '/profiles' => '?user.collaborations.camp='.$camp,
            '/schedule_entries' => '?period='.$period,
            default => '?camp='.$camp,
        };
    }
}
