<?php

namespace App\Tests\Api\Categories;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCategoryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchCategoryIsAllowedForCollaborator() {
        $category = static::$fixtures['category1'];
        $response = static::createClientWithCredentials()->request('PATCH', '/categories/'.$category->getId(), ['json' => [
            'short' => 'LP',
            'name' => 'Lagerprogramm',
            'color' => '#FFFF00',
            'numberingStyle' => 'I',
            'preferredContentTypes' => [
                $this->getIriFor('contentTypeColumnLayout'),
                $this->getIriFor('contentTypeSafetyConcept'),
            ],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'short' => 'LP',
            'name' => 'Lagerprogramm',
            'color' => '#FFFF00',
            'numberingStyle' => 'I',
            '_links' => [
                'preferredContentTypes' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('contentTypeColumnLayout')],
            ['href' => $this->getIriFor('contentTypeSafetyConcept')],
        ], $response->toArray()['_links']['preferredContentTypes']);
    }

    public function testPatchCategoryDisallowsChangingCamp() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('PATCH', '/categories/'.$category->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchCategoryValidatesInvalidColor() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('PATCH', '/categories/'.$category->getId(), ['json' => [
            'color' => 'green',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchCategoryValidatesInvalidNumberingStyle() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('PATCH', '/categories/'.$category->getId(), ['json' => [
            'numberingStyle' => 'X',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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
}
