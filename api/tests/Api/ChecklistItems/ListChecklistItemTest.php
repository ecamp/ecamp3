<?php

namespace App\Tests\Api\ChecklistItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListChecklistItemTest extends ECampApiTestCase {
    public function testListChecklistItemsIsDeniedForAnonymousUser() {
        $response = static::createBasicClient()->request('GET', '/checklist_items');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListChecklistItemsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a checklist-item that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['checklistItemUnrelated_1_1']);

        $response = static::createClientWithCredentials()->request('GET', '/checklist_items');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 6,
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
            ['href' => $this->getIriFor('checklistItem2_1_1')],
            ['href' => $this->getIriFor('checklistItemPrototype_1_1')],
        ], $response->toArray()['_links']['items']);
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
            ['href' => $this->getIriFor('checklistItemPrototype_1_1')],
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
}
