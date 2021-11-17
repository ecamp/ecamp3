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

    public function testCreateValidatesIncompatibleContentType() {
        // given
        /** @var ContentType $contentType */
        $contentType = static::$fixtures[ContentNode\ColumnLayout::class === $this->entityClass ? 'contentTypeSafetyConcept' : 'contentTypeColumnLayout'];

        // when sending no prototype, but a content type that does not fit the entity class
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

    public function testCreateValidatesIncompatibleContentTypeFromPrototype() {
        // given
        // Create the client at the beginning of the test, because creating the client resets the DB
        $client = static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()]);

        $prototypeContentType = static::$fixtures[(ContentNode\ColumnLayout::class == $this->entityClass) ? 'contentTypeSafetyConcept' : 'contentTypeColumnLayout'];
        // Re-fetch the content type from the db, so Doctrine isn't confused
        /** @var ContentType $prototypeContentType */
        $prototypeContentType = $this->getEntityManager()->getRepository(ContentType::class)->find($prototypeContentType->getId());

        // Use a prototype that matches the current entity class, but has a different content type
        /** @var ContentNode $prototype */
        $prototype = $this->getEntityManager()->getRepository($this->entityClass)->findOneBy([]);
        $prototype->contentType = $prototypeContentType;
        $this->getEntityManager()->persist($prototype);
        $this->getEntityManager()->flush();

        // when sending no content type, but a prototype with a content type that does not fit the entity class (inconsistent prototype)
        $client->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([
            'prototype' => $this->getIriFor($prototype),
        ], ['contentType'])]);

        // then
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [], // remove this line once https://github.com/symfony/symfony/issues/35399 is fixed
                [
                    'propertyPath' => 'contentType',
                    'message' => "Selected contentType {$prototypeContentType->name} is incompatible with entity of type {$this->entityClass} (it can only be used with entities of type {$prototypeContentType->entityClass}).",
                ],
            ],
        ]);
    }

    public function testCreateWithoutContentTypeUsesContentTypeFromPrototype() {
        $this->markTestSkipped('This does not work as long as DisableAutoMapping is not inherited in subclasses, see https://github.com/symfony/symfony/issues/35399. Workaround would be to manually add DisableAutoMapping on the contentType property on all child classes of ContentNode.');

        // given

        // Use a prototype that matches the current entity class
        /** @var ContentNode $prototype */
        $prototype = $this->getEntityManager()->getRepository($this->entityClass)->findOneBy([]);

        // when sending no content type, but a prototype with a content type
        $response = $this->create($this->getExampleWritePayload(['prototype' => $this->getIriFor($prototype)], ['contentType']));

        // then
        $id = $response->toArray()['id'];
        $newContentNode = $this->getEntityManager()->getRepository($this->entityClass)->find($id);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload($newContentNode), true);
    }

    public function testCreateFromPrototypeValidatesWrongPrototypeType() {
        // given
        // Use a prototype that does not match the current entity class
        $prototype = (ContentNode\ColumnLayout::class === $this->entityClass) ? 'multiSelect1' : 'columnLayout1';
        $prototypeClass = get_class(static::$fixtures[$prototype]);
        $entityClass = $this->entityClass;

        // when sending a prototype of a different type (e.g. SingleText vs. MultiSelect)
        $this->create($this->getExampleWritePayload([
            'prototype' => $this->getIriFor($prototype),
        ]), static::$fixtures['user2member']);

        // then
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'prototype',
                    'message' => "This value must be an instance of {$entityClass} or a subclass, but was {$prototypeClass}.",
                ],
            ],
        ]);
    }

    public function testCreateFromPrototypeValidatesWrongPrototypeContentType() {
        // given
        // Create the client at the beginning of the test, because creating the client resets the DB
        $client = static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()]);

        $prototypeContentType = static::$fixtures[(ContentNode\ColumnLayout::class == $this->entityClass) ? 'contentTypeSafetyConcept' : 'contentTypeColumnLayout'];
        // Re-fetch the content type from the db, so Doctrine isn't confused
        /** @var ContentType $prototypeContentType */
        $prototypeContentType = $this->getEntityManager()->getRepository(ContentType::class)->find($prototypeContentType->getId());

        // Use a prototype that matches the current entity class, but has a different content type
        /** @var ContentNode $prototype */
        $prototype = $this->getEntityManager()->getRepository($this->entityClass)->findOneBy([]);
        $prototype->contentType = $prototypeContentType;
        $this->getEntityManager()->persist($prototype);
        $this->getEntityManager()->flush();

        // when sending a prototype of a matching type but with conflicting contentType
        // E.g. SingleText with contentType Notes vs. SingleText with contentType SafetyConcept
        $client->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([
            'prototype' => $this->getIriFor($prototype),
        ])]);

        // then
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'prototype',
                    'message' => "This value must have the content type {$this->defaultContentType->name}, but was {$prototypeContentType->name}.",
                ],
            ],
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'parent' => $this->getIriFor($this->defaultParent),
                'contentType' => $this->getIriFor($this->defaultContentType),
                'position' => 10,
                'prototype' => null,
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
            'slot' => 'footer',
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
                'children' => [
                    'href' => '/content_nodes?parent='.$this->getIriFor($self),
                ],
                'contentType' => [
                    'href' => $this->getIriFor($contentType),
                ],
                'owner' => [
                    'href' => $this->getIriFor($parent->getRootOwner()),
                ],
                'ownerCategory' => [
                    'href' => $this->getIriFor($parent->getOwnerCategory()),
                ],
            ],
        ];
    }
}
