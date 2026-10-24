<?php

namespace App\Tests\Api\ChecklistItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListChecklistItemsTest extends ECampApiTestCase {
    public function testListChecklistItemsIsSortedByPositionAndId() {
        $checklistId = static::getFixture('checklist1')->getId();
        $this->assertChecklistItemsAreSortedByPositionAndId('/checklist_items?checklist=%2Fchecklists%2F'.$checklistId);
    }

    public function testListChecklistItemsAsChecklistSubresourceIsSortedByPositionAndId() {
        $checklistId = static::getFixture('checklist1')->getId();
        $this->assertChecklistItemsAreSortedByPositionAndId('/checklists/'.$checklistId.'/checklist_items');
    }

    public function testListChecklistItemsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/checklist_items');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListChecklistItemsWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a checklist-item that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['checklistItemUnrelated_1_1']);

        static::createClientWithCredentials()->request('GET', '/checklist_items');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListChecklistItemsFilteredByChecklistIsAllowedForCollaborator() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials()->request('GET', '/checklist_items?checklist=%2Fchecklists%2F'.$checklist->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistItem1_1_1')],
            ['href' => $this->getIriFor('checklistItem1_1_2')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3_4')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistItemsFilteredByChecklistIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklist_items?checklist=%2Fchecklists%2F'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListChecklistItemsFilteredByChecklistIsDeniedForInactiveCollaborator() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/checklist_items?checklist=%2Fchecklists%2F'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListChecklistItemsFilteredByChecklistPrototypeIsAllowedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/checklist_items?checklist=%2Fchecklists%2F'.$checklist->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistItemCampPrototype_1_1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistItemsAsChecklistSubresourceIsAllowedForCollaborator() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials()->request('GET', '/checklists/'.$checklist->getId().'/checklist_items');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistItem1_1_1')],
            ['href' => $this->getIriFor('checklistItem1_1_2')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3_4')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistItemsAsChecklistSubresourceIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklists/'.$checklist->getId().'/checklist_items')
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListChecklistItemsAsChecklistNodeSubresourceIsAllowedForCollaborator() {
        $checklistNode = static::getFixture('checklistNode1');
        $response = static::createClientWithCredentials()->request('GET', '/content_node/checklist_nodes/'.$checklistNode->getId().'/checklist_items');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistItem1_1_1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistItemsAsChecklistNodeSubresourceIsDeniedForUnrelatedUser() {
        $checklistNode = static::getFixture('checklistNode1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/content_node/checklist_nodes/'.$checklistNode->getId().'/checklist_items')
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListChecklistItemsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/checklist_items?checklist.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 4]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistItem1_1_1')],
            ['href' => $this->getIriFor('checklistItem1_1_2')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3')],
            ['href' => $this->getIriFor('checklistItem1_1_2_3_4')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistItemsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklist_items?checklist.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    private function assertChecklistItemsAreSortedByPositionAndId(string $url): void {
        $items = [
            static::getFixture('checklistItem1_1_1'),
            static::getFixture('checklistItem1_1_2'),
            static::getFixture('checklistItem1_1_2_3'),
            static::getFixture('checklistItem1_1_2_3_4'),
        ];
        foreach ($items as $index => $item) {
            // Set explicit positions without the sortable listener renumbering them.
            $this->getEntityManager()->getConnection()->executeStatement(
                'UPDATE checklist_item SET position = :position WHERE id = :id',
                ['position' => 0 === $index % 2 ? 10 : 2, 'id' => $item->getId()]
            );
        }
        $this->getEntityManager()->clear();

        $response = static::createClientWithCredentials()->request('GET', $url);

        $this->assertResponseStatusCodeSame(200);
        $result = $response->toArray()['_embedded']['items'];
        $this->assertSame([2, 2, 10, 10], array_column($result, 'position'));
        $lowerPositions = [$items[1]->getId(), $items[3]->getId()];
        $higherPositions = [$items[0]->getId(), $items[2]->getId()];
        sort($lowerPositions);
        sort($higherPositions);
        $this->assertSame([...$lowerPositions, ...$higherPositions], array_column($result, 'id'));
    }
}
