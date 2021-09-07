<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testPatchActivityIsDeniedForAnonymousUser() {
        $activity = static::$fixtures['activity1'];
        static::createClient()->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
            'title' => 'Hello World',
            'location' => 'Stoos',
            'category' => $this->getIriFor('category2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchActivityIsDeniedForUnrelatedUser() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => 'Hello World',
                'location' => 'Stoos',
                'category' => $this->getIriFor('category2'),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchActivityIsDeniedForGuest() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => 'Hello World',
                'location' => 'Stoos',
                'category' => $this->getIriFor('category2'),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchActivityIsAllowedForMember() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => 'Hello World',
                'location' => 'Stoos',
                'category' => $this->getIriFor('category2'),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
            'location' => 'Stoos',
            '_links' => [
                'category' => ['href' => $this->getIriFor('category2')],
            ],
        ]);
    }

    public function testPatchActivityIsAllowedForManager() {
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

    public function testPatchActivityFromCampPrototypeIsDeniedForUnrelatedUser() {
        $activity = static::$fixtures['activity1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
            'title' => 'Hello World',
            'location' => 'Stoos',
            'category' => $this->getIriFor('category2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
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
