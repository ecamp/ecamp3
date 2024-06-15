<?php

namespace App\Tests\Api\ChecklistItems;

use App\Entity\ChecklistItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadChecklistItemTest extends ECampApiTestCase {
    public function testGetSingleChecklistItemIsDeniedForAnonymousUser() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createBasicClient()->request('GET', '/checklist_items/'.$checklistItem->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleChecklistItemIsDeniedForUnrelatedUser() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklist_items/'.$checklistItem->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleChecklistItemIsDeniedForInactiveCollaborator() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/checklist_items/'.$checklistItem->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleChecklistItemIsAllowedForGuest() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/checklist_items/'.$checklistItem->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklistItem->getId(),
            'text' => $checklistItem->text,
            '_links' => [
                'checklist' => ['href' => $this->getIriFor('checklist1')],
            ],
        ]);
    }

    public function testGetSingleChecklistItemIsAllowedForMember() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/checklist_items/'.$checklistItem->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklistItem->getId(),
            'text' => $checklistItem->text,
            '_links' => [
                'checklist' => ['href' => $this->getIriFor('checklist1')],
            ],
        ]);
    }

    public function testGetSingleChecklistItemIsAllowedForManager() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request('GET', '/checklist_items/'.$checklistItem->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklistItem->getId(),
            'text' => $checklistItem->text,
            '_links' => [
                'checklist' => ['href' => $this->getIriFor('checklist1')],
            ],
        ]);
    }

    public function testGetSingleChecklistItemFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var ChecklistItem $checklistItem */
        $checklistItem = static::getFixture('checklistItemPrototype_1_1');
        static::createClientWithCredentials()->request('GET', '/checklist_items/'.$checklistItem->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklistItem->getId(),
            'text' => $checklistItem->text,
            '_links' => [
                'checklist' => ['href' => $this->getIriFor('checklist1campPrototype')],
            ],
        ]);
    }
}
