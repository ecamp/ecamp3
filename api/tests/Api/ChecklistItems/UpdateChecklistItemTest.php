<?php

namespace App\Tests\Api\ChecklistItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateChecklistItemTest extends ECampApiTestCase {
    public function testPatchChecklistItemIsDeniedForAnonymousUser() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createBasicClient()->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
            'text' => 'Ziel 2',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchChecklistItemIsDeniedForUnrelatedUser() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
                'text' => 'Ziel 2',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchChecklistItemIsDeniedForInactiveCollaborator() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
                'text' => 'Ziel 2',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchChecklistItemIsDeniedForGuest() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
                'text' => 'Ziel 2',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchChecklistItemIsAllowedForMember() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
                'text' => 'Ziel 2',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => 'Ziel 2',
        ]);
    }

    public function testPatchChecklistItemIsAllowedForManager() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        $response = static::createClientWithCredentials()->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
            'text' => 'Ziel 2',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => 'Ziel 2',
        ]);
    }

    public function testPatchChecklistItemInCampPrototypeIsDeniedForUnrelatedUser() {
        $checklistItem = static::getFixture('checklistItemPrototype_1_1');
        $response = static::createClientWithCredentials()->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
            'text' => 'Ziel 2',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchChecklistItemDisallowsChangingChecklist() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request('PATCH', '/checklist_items/'.$checklistItem->getId(), ['json' => [
            'checklist' => $this->getIriFor('checklistItem2_1_1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("checklist" is unknown).',
        ]);
    }

    public function testPatchChecklistItemValidatesNoParentLoop() {
        $checklistItemParent = static::getFixture('checklistItem1_1_2');
        $checklistItemChild = static::getFixture('checklistItem1_1_2_3');

        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItemParent->getId(),
            [
                'json' => [
                    'parent' => '/checklist_items/'.$checklistItemChild->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'parent: Must not form a loop of parent-child relations.
parent: Nesting can be a maximum of 3 levels deep.',
        ]);
    }

    public function testPatchChecklistItemIsDeniedForTooDeepNesting() {
        $checklistItem = static::getFixture('checklistItem1_1_2');
        $checklistItemNewParent = static::getFixture('checklistItem1_1_1');

        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'parent' => '/checklist_items/'.$checklistItemNewParent->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'parent: Nesting can be a maximum of 3 levels deep.',
        ]);
    }

    public function testPatchChecklistItemValidatesNullText() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'text' => null,
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "text" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchChecklistItemValidatesBlankText() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'text' => ' ',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'text',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchChecklistItemValidatesTooLongText() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'text' => str_repeat('l', 257),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'text',
                    'message' => 'This value is too long. It should have 256 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchChecklistItemTrimsText() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'text' => "  \t Ziel 2\t ",
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'], ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(
            [
                'text' => 'Ziel 2',
            ]
        );
    }

    public function testPatchChecklistItemCleansForbiddenCharactersFromText() {
        $checklistItem = static::getFixture('checklistItem1_1_1');
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $client->request(
            'PATCH',
            '/checklist_items/'.$checklistItem->getId(),
            [
                'json' => [
                    'text' => "<b>Ziel</b>2\n\t<a>",
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'], ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(
            [
                'text' => '<b>Ziel</b>2<a>',
            ]
        );
    }
}
