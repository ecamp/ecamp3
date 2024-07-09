<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;

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

        $this->defaultParent = static::getFixture('columnLayout1');
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

    #[DataProvider('getContentNodesWhichCannotHaveChildren')]
    public function testCreateRejectsParentsWhichDontSupportChildren(string $idOfParentFixture) {
        $this->defaultParent = static::getFixture($idOfParentFixture);

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
        $contentType = static::getFixture(ColumnLayout::class === $this->entityClass ? 'contentTypeSafetyConcept' : 'contentTypeColumnLayout');

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
                    'message' => 'This value should be one of [1], was invalidSlot.',
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
                    'message' => 'This value should be one of [1], was null.',
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

    public function testCreatePutsContentNodeAtEndOfSlot() {
        $this->create($this->getExampleWritePayload(
            [
                'parent' => $this->getIriFor('columnLayout1'),
                'slot' => '1',
            ],
            ['position']
        ));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'slot' => '1',
            'position' => 1,
        ]);
    }

    public function testCreateRejectsTooLongInstanceName() {
        $this->create($this->getExampleWritePayload(
            [
                'instanceName' => str_repeat('a', 33),
            ]
        ));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'instanceName: This value is too long. It should have 32 characters or less.',
        ]);
    }

    public function testCreateTrimsInstanceName() {
        $this->create($this->getExampleWritePayload(
            [
                'instanceName' => " SchlechtwetterProgramm\t\t",
            ]
        ));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'instanceName' => 'SchlechtwetterProgramm',
        ]);
    }

    public function testCreateCleansTextOfInstanceName() {
        $this->create($this->getExampleWritePayload(
            [
                'instanceName' => "\u{000A}control\u{0007}",
            ]
        ));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'instanceName' => 'control',
        ]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateResponseStructureMatchesReadResponseStructure() {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $createResponse = $client->request(
            'POST',
            $this->endpoint,
            [
                'json' => $this->getExampleWritePayload(),
            ]
        );

        $this->assertResponseStatusCodeSame(201);

        $createArray = $createResponse->toArray();
        $newItemLink = $createArray['_links']['self']['href'];
        $getItemResponse = $client->request('GET', $newItemLink);

        assertThat($createArray, CompatibleHalResponse::isHalCompatibleWith($getItemResponse->toArray()));
    }

    public function testCreatePurgesCacheTags() {
        $client = static::createClientWithCredentials();
        $cacheManager = $this->mockCacheManager();

        $client->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        self::assertEqualsCanonicalizing([
            '/content_nodes',
            $this->endpoint,
            $this->defaultParent->getRoot()->getId().'#rootDescendants',
            $this->defaultParent->getId().'#children',
        ], $cacheManager->getInvalidatedTags());
    }

    public static function getContentNodesWhichCannotHaveChildren(): array {
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
    protected function getExampleReadPayload(ContentNode $self, array $attributes = [], array $except = []) {
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
}
