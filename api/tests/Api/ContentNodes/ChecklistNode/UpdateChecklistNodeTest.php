<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Entity\ContentNode\ChecklistNode;
use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateChecklistNodeTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';
        $this->defaultEntity = static::getFixture('checklistNode1');
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
                    1 => [
                        'href' => '/checklist_items/'.$checklistItemId,
                    ],
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
                    1 => [
                        'href' => '/checklist_items/'.$checklistItemId,
                    ],
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
        $this->assertFalse(in_array($checklistItem, $checklistNode->getChecklistItems()));
    }

    public function testRemoveChecklistItemForManager() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), ['json' => [
            'removeChecklistItemIds' => [$checklistItem->getId()],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $checklistNode = $this->getEntityManager()->getRepository(ChecklistNode::class)->find($this->defaultEntity->getId());
        $this->assertFalse(in_array($checklistItem, $checklistNode->getChecklistItems()));
    }
}
