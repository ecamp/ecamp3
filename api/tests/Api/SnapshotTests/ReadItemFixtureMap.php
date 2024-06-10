<?php

namespace App\Tests\Api\SnapshotTests;

class ReadItemFixtureMap {
    public static function get(string $collectionEndpoint, array $fixtures): mixed {
        return match ($collectionEndpoint) {
            '/activities' => $fixtures['activity1'],
            '/activity_progress_labels' => $fixtures['activityProgressLabel1'],
            '/activity_responsibles' => $fixtures['activityResponsible1'],
            '/camp_collaborations' => $fixtures['campCollaboration1manager'],
            '/camps' => $fixtures['camp1'],
            '/categories' => $fixtures['category1'],
            '/checklists' => $fixtures['checklist1'],
            '/content_node/column_layouts' => $fixtures['columnLayout2'],
            '/content_node/responsive_layouts' => $fixtures['responsiveLayout1'],
            '/content_types' => $fixtures['contentTypeSafetyConcept'],
            '/day_responsibles' => $fixtures['dayResponsible1'],
            '/days' => $fixtures['day1period1'],
            '/material_items' => $fixtures['materialItem1'],
            '/material_lists' => $fixtures['materialList1'],
            '/content_node/material_nodes' => $fixtures['materialNode2'],
            '/content_node/multi_selects' => $fixtures['multiSelect1'],
            '/periods' => $fixtures['period1'],
            '/profiles' => $fixtures['profile1manager'],
            '/schedule_entries' => $fixtures['scheduleEntry1period1camp1'],
            '/content_node/single_texts' => $fixtures['singleText1'],
            '/content_node/storyboards' => $fixtures['storyboard1'],
            '/users' => $fixtures['user1manager'],
            default => throw new \RuntimeException("no fixture defined for endpoint {$collectionEndpoint}")
        };
    }
}
