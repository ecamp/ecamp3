<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchActivityIsAllowedForCollaborator() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
            'title' => 'Hello World',
            'location' => 'Stoos',
            'category' => $this->getIriFor('category2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
            'location' => 'Stoos',
            '_links' => [
                'category' => ['href' => $this->getIriFor('category2')],
            ],
        ]);
    }

    public function testPatchActivityValidatesCategoryFromSameCamp() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
            'category' => $this->getIriFor('category1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'category',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }
}
