<?php

namespace App\Tests\Api\ContentNodes;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateContentNodeTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function setUp(): void {
        $this->markTestSkipped('Tests temporarily inactive (rewritings tests TBD)');
    }

    public function testCreateContentNodeIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateContentNodeSetsRootToParentsRoot() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'root' => ['href' => '/content_nodes/'.static::$fixtures['columnLayout1']->root->getId()],
        ]]);
    }

    public function testCreateContentNodeValidatesMissingParent() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['parent'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must not be null on non-root content nodes.',
                ],
            ],
        ]);
    }

    public function testCreateContentNodeAllowsMissingSlot() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['slot'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['slot' => null]);
    }

    public function testCreateContentNodeAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => null]);
    }

    public function testCreateContentNodeAllowsMissingJsonConfig() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['jsonConfig'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['jsonConfig' => null]);
    }

    public function testCreateContentNodeAllowsMissingInstanceName() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['instanceName'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['instanceName' => null]);
    }

    public function testCreateContentNodeValidatesMissingContentType() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['contentType'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentType',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ContentNode::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'parent' => $this->getIriFor('columnLayout1'),
                'contentType' => $this->getIriFor('contentTypeColumnLayout'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ContentNode::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['parent', 'contentType'],
            $except
        );
    }
}
