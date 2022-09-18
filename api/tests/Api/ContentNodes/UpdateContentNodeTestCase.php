<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base UPDATE (patch) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class UpdateContentNodeTestCase extends ECampApiTestCase {
    public function testPatchIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('PATCH', "{$this->endpoint}/".$this->defaultEntity->getId(), ['json' => [], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchIsDeniedForInvitedCollaborator() {
        $this->patch(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForInactiveCollaborator() {
        $this->patch(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForUnrelatedUser() {
        $this->patch(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForGuest() {
        $this->patch(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsAllowedForMember() {
        $this->patch(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchIsAllowedForManager() {
        $this->patch(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider getContentNodesWhichCannotHaveChildren
     */
    public function testPatchRejectsParentsWhichDontSupportChildren(string $idOfParentFixture) {
        $parentIri = static::getIriFor($idOfParentFixture);

        $this->patch(payload: ['parent' => $parentIri], user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'parent',
                    'message' => 'This parent does not support children, only content_nodes of type column_layout support children.',
                ],
            ],
        ]);
    }

    public function testPatchValidatesThatParentSupportsSlotName() {
        $this->patch(payload: ['slot' => 'invalidSlot']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'slot',
                    'message' => 'This value should be one of [1,2], was invalidSlot.',
                ],
            ],
        ]);
    }

    public function testPatchRejectsNullSlotOnNonRootNodes() {
        if ($this->defaultEntity instanceof ColumnLayout) {
            $this->defaultEntity = static::$fixtures['columnLayoutChild1'];
        }
        $this->patch(
            payload: [
                'slot' => null,
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'slot',
                    'message' => 'This value should be one of [1,2], was null.',
                ],
            ],
        ]);
    }

    private static function getContentNodesWhichCannotHaveChildren(): array {
        return [
            ContentNode\MaterialNode::class => [
                'materialNode1',
            ],
            ContentNode\MultiSelect::class => [
                'multiSelect1',
            ],
            ContentNode\SingleText::class => [
                'singleText1',
            ],
            ContentNode\StoryBoard::class => [
                'storyboard1',
            ],
        ];
    }
}
