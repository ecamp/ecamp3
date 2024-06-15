<?php

namespace App\Tests\Api\ChecklistItems;

use App\Entity\ChecklistItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteChecklistItemTest extends ECampApiTestCase {
    public function testDeleteChecklistItemIsDeniedForAnonymousUser() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createBasicClient()->request('DELETE', '/checklist_items/'.$checklistItem->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteChecklistItemIsDeniedForUnrelatedUser() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/checklist_items/'.$checklistItem->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistItemIsDeniedForInactiveCollaborator() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/checklist_items/'.$checklistItem->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistItemIsDeniedForGuest() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/checklist_items/'.$checklistItem->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistItemIsAllowedForMember() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/checklist_items/'.$checklistItem->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ChecklistItem::class)->find($checklistItem->getId()));
    }

    public function testDeleteChecklistItemIsAllowedForManager() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials()->request('DELETE', '/checklist_items/'.$checklistItem->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ChecklistItem::class)->find($checklistItem->getId()));
    }

    public function testDeleteChecklistItemFromCampPrototypeIsDeniedForUnrelatedUser() {
        $checklistItem = static::getFixture('checklistItemPrototype_1_1');
        static::createClientWithCredentials()->request('DELETE', '/checklist_items/'.$checklistItem->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
