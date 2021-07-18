<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCategoryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateCategoryIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCategoryCreatesNewColumnLayoutAsRootContentNode() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $newestColumnLayout = $this->getEntityManager()->getRepository(ContentNode::class)
            ->findBy(['contentType' => static::$fixtures['contentTypeColumnLayout']], ['createTime' => 'DESC'])[0];
        $this->assertJsonContains(['_links' => [
            'rootContentNode' => ['href' => '/content_nodes/'.$newestColumnLayout->getId()],
        ]]);
    }

    public function testCreateCampDoesntExposeCampPrototypeId() {
        $response = static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['preferredContentTypes'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testCreateCategoryValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryAllowsEmptyPreferredContentTypes() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['preferredContentTypes'])]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateCategoryValidatesMissingShort() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['short'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'short',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['name'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesMissingColor() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['color'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'color',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesInvalidColor() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([
            'color' => 'red',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'color',
                    'message' => 'This value is not valid.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryUsesDefaultForMissingNumberingStyle() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['numberingStyle'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['numberingStyle' => '1']);
    }

    public function testCreateCategoryValidatesInvalidNumberingStyle() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([
            'numberingStyle' => 'x',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'numberingStyle',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Category::class,
            array_merge([
                'camp' => $this->getIriFor('camp1'),
                'preferredContentTypes' => [$this->getIriFor('contentType1')],
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Category::class,
            array_merge([
                '_links' => [
                    'contentNodes' => [],
                ],
            ], $attributes),
            ['camp', 'preferredContentTypes'],
            $except
        );
    }
}
