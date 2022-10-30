<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Entity\ContentType;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base CREATE (post) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class CreateContentNodeTestCase extends ECampApiTestCase {
    protected ContentType $defaultContentType;

    protected ContentNode $defaultParent;

    public function setUp(): void {
        parent::setUp();

        $this->defaultParent = static::$fixtures['columnLayout1'];
    }

    public function testCreateIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateIsDeniedForInvitedCollaborator() {
        $this->create(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Item not found for \"{$this->getIriFor($this->defaultParent)}\".",
        ]);
    }

    public function testCreateIsDeniedForInactiveCollaborator() {
        $this->create(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Item not found for \"{$this->getIriFor($this->defaultParent)}\".",
        ]);
    }

    public function testCreateIsDeniedForUnrelatedUser() {
        $this->create(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Item not found for \"{$this->getIriFor($this->defaultParent)}\".",
        ]);
    }

    public function testCreateIsDeniedForGuest() {
        $this->create(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateIsAllowedForMember() {
        $this->create(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateIsAllowedForManager() {
        // when
        $response = $this->create(user: static::$fixtures['user1manager']);

        // then
        $id = $response->toArray()['id'];
        $newContentNode = $this->getEntityManager()->getRepository($this->entityClass)->find($id);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload($newContentNode), true);
    }

    /**
     * @dataProvider getContentNodesWhichCannotHaveChildren
     */
    public function testCreateRejectsParentsWhichDontSupportChildren(string $idOfParentFixture) {
        $this->defaultParent = static::$fixtures[$idOfParentFixture];

        $this->create(user: static::$fixtures['user2member']);
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

    public function testCreateValidatesIncompatibleContentType() {
        // given
        /** @var ContentType $contentType */
        $contentType = static::$fixtures[ContentNode\ColumnLayout::class === $this->entityClass ? 'contentTypeSafetyConcept' : 'contentTypeColumnLayout'];

        // when
        $this->create($this->getExampleWritePayload(['contentType' => $this->getIriFor($contentType)]));

        // then
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentType',
                    'message' => "Selected contentType {$contentType->name} is incompatible with entity of type {$this->entityClass} (it can only be used with entities of type {$contentType->entityClass}).",
                ],
            ],
        ]);
    }

    public function testCreateValidatesThatParentSupportsSlotName() {
        $this->create($this->getExampleWritePayload(['slot' => 'invalidSlot']));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'slot',
                    'message' => 'This value should be one of [1,2], was invalidSlot.',
                ],
            ],
        ]);
    }

    public function testCreateContentNodeRejectsMissingSlot() {
        $this->create($this->getExampleWritePayload([], ['slot']));

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

    public function testCreateResortsEntriesIfExistingPositionWasUsed() {
        $this->create($this->getExampleWritePayload(
            [
                'parent' => $this->getIriFor('columnLayout1'),
                'slot' => '1',
                'position' => 0,
            ]
        ));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'slot' => '1',
            'position' => 0,
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'parent' => $this->getIriFor($this->defaultParent),
                'contentType' => $this->getIriFor($this->defaultContentType),
                'position' => 10,
            ], $attributes),
            $except
        );
    }

    /**
     * Payload setup.
     */
    protected function getExampleReadPayload(ContentNode $self, $attributes = [], $except = []) {
        /** @var ContentNode $parent */
        $parent = $this->defaultParent;

        /** @var ContentType $contentType */
        $contentType = $this->defaultContentType;

        return [
            'slot' => '1',
            'position' => 10,
            'instanceName' => 'Schlechtwetterprogramm',
            'contentTypeName' => $contentType->name,
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor($self),
                ],
                'root' => [
                    'href' => $this->getIriFor($parent->root),
                ],
                'parent' => [
                    'href' => $this->getIriFor($parent),
                ],
                'children' => [],
                'contentType' => [
                    'href' => $this->getIriFor($contentType),
                ],
            ],
        ];
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
