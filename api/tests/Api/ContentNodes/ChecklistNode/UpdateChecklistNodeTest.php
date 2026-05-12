<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Entity\ContentNode\ChecklistNode;
use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateChecklistNodeTest extends UpdateContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';
        $this->defaultEntity = static::getFixture('checklistNode1');
        $this->campPrototypeEntity = static::getFixture('checklistNodeCampPrototype');
        $this->sharedCampEntity = static::getFixture('checklistNodeCampShared');
    }

    public function testAddChecklistItemIsDeniedForGuest() {
        $checklistItemId = static::getFixture('checklistItem1_1_2')->getId();
        static::createClientWithCredentials(['email' => static::getFixture('user3guest')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'addChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testAddChecklistItemForMember() {
        $checklistItemId = static::getFixture('checklistItem1_1_2')->getId();
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'addChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'checklistItems' => [
                    'href' => '/checklist_items?checklistNodes=%2Fcontent_node%2Fchecklist_nodes%2F'.$this->defaultEntity->getId(),
                ],
            ],
        ]);
    }

    public function testAddChecklistItemForManager() {
        $checklistItemId = static::getFixture('checklistItem1_1_2')->getId();
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'addChecklistItemIds' => [$checklistItemId],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'checklistItems' => [
                    'href' => '/checklist_items?checklistNodes=%2Fcontent_node%2Fchecklist_nodes%2F'.$this->defaultEntity->getId(),
                ],
            ],
        ]);
    }

    public function testRemoveChecklistItemIsDeniedForGuest() {
        $checklistItemId = static::getFixture('checklistItem1_1_1')->getId();
        static::createClientWithCredentials(['email' => static::getFixture('user3guest')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testRemoveChecklistItemForMember() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItem->getId()],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $this->assertNotContains($checklistItem, $checklistNode->getChecklistItems());
    }

    public function testRemoveChecklistItemForManager() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'removeChecklistItemIds' => [$checklistItem->getId()],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $this->assertNotContains($checklistItem, $checklistNode->getChecklistItems());
    }

    public function testRemoveChecklistItemInCampPrototypeIsDeniedForUnrelatedUser() {
        $entity = static::getFixture('checklistNodeCampPrototype');
        $checklistItemId = static::getFixture('checklistItemCampPrototype_1_1')->getId();
        static::createClientWithCredentials()
            ->request('PATCH', $this->endpoint.'/'.$entity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testRemoveChecklistItemInSharedCampIsDeniedForUnrelatedUser() {
        $entity = static::getFixture('checklistNodeCampShared');
        $checklistItemId = static::getFixture('checklistItemCampShared_1_1')->getId();
        static::createClientWithCredentials()
            ->request('PATCH', $this->endpoint.'/'.$entity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testRemoveChecklistItemInSharedCampIsDeniedForInactiveUser() {
        $entity = static::getFixture('checklistNodeCampShared');
        $checklistItemId = static::getFixture('checklistItemCampShared_1_1')->getId();
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$entity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testRemoveChecklistItemInSharedCampIsDeniedForInvitedUser() {
        $entity = static::getFixture('checklistNodeCampShared');
        $checklistItemId = static::getFixture('checklistItemCampShared_1_1')->getId();
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$entity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testAddMultipleChecklistItemsForMember() {
        $checklistItem1 = static::getFixture('checklistItem1_1_2');
        $checklistItem2 = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'addChecklistItemIds' => [$checklistItem1->getId(), $checklistItem2->getId()],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'checklistItems' => [
                    'href' => '/checklist_items?checklistNodes=%2Fcontent_node%2Fchecklist_nodes%2F'.$this->defaultEntity->getId(),
                ],
            ],
        ]);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $actualIds = array_map(fn ($item) => $item->getId(), $checklistNode->getChecklistItems());
        $this->assertContains($checklistItem1->getId(), $actualIds);
        $this->assertContains($checklistItem2->getId(), $actualIds);
    }

    public function testAddMultipleChecklistItemsForManager() {
        $checklistItem1 = static::getFixture('checklistItem1_1_2');
        $checklistItem2 = static::getFixture('checklistItem1_1_2_3');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'addChecklistItemIds' => [$checklistItem1->getId(), $checklistItem2->getId()],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'checklistItems' => [
                    'href' => '/checklist_items?checklistNodes=%2Fcontent_node%2Fchecklist_nodes%2F'.$this->defaultEntity->getId(),
                ],
            ],
        ]);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $actualIds = array_map(fn ($item) => $item->getId(), $checklistNode->getChecklistItems());
        $this->assertContains($checklistItem1->getId(), $actualIds);
        $this->assertContains($checklistItem2->getId(), $actualIds);
    }

    public function testRemoveMultipleChecklistItemsForMember() {
        $checklistItem1 = static::getFixture('checklistItem1_1_1');
        $checklistItem2 = static::getFixture('checklistItem1_1_2');
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'addChecklistItemIds' => [$checklistItem2->getId()],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'removeChecklistItemIds' => [$checklistItem1->getId(), $checklistItem2->getId()],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $actualIds = array_map(fn ($item) => $item->getId(), $checklistNode->getChecklistItems());
        $this->assertNotContains($checklistItem1->getId(), $actualIds);
        $this->assertNotContains($checklistItem2->getId(), $actualIds);
    }

    public function testRemoveMultipleChecklistItemsForManager() {
        $checklistItem1 = static::getFixture('checklistItem1_1_1');
        $checklistItem2 = static::getFixture('checklistItem1_1_2');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'addChecklistItemIds' => [$checklistItem2->getId()],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'removeChecklistItemIds' => [$checklistItem1->getId(), $checklistItem2->getId()],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $actualIds = array_map(fn ($item) => $item->getId(), $checklistNode->getChecklistItems());
        $this->assertNotContains($checklistItem1->getId(), $actualIds);
        $this->assertNotContains($checklistItem2->getId(), $actualIds);
    }

    public function testAddChecklistItemOfOtherCampIsDenied() {
        $checklistItemId = static::getFixture('checklistItem2_1_1')->getId();
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
            ->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
                'addChecklistItemIds' => [$checklistItemId],
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'addChecklistItemIds: Must belong to the same camp.',
        ]);
    }
}
