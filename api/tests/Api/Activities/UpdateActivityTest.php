<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityTest extends ECampApiTestCase {
    public function testPatchActivityIsDeniedForAnonymousUser() {
        $activity = static::$fixtures['activity1'];
        static::createBasicClient()->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
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
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
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

    public function testPatchActivityIsDeniedForInactiveCollaborator() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
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
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
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
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

    public function testPatchActivityValidatesNullTitle() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => null,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "title" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchActivityValidatesTitleMinLength() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => '',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchActivityValidatesTitleMaxLength() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => str_repeat('a', 33),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchActivityCleansHtmlFromTitle() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => 'Dschungel<script>alert(1)</script>buch',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Dschungelbuch',
        ]);
    }

    public function testPatchActivityTrimsTitle() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => " \t".str_repeat('a', 32)." \t",
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => str_repeat('a', 32),
        ]);
    }

    public function testPatchActivityValidatesNullLocation() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => null,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "location" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchActivityAllowsSettingLocationToEmptyString() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => '',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'location' => '',
        ]);
    }

    public function testPatchActivityValidatesLocationMaxLength() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => str_repeat('a', 65),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'location',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchActivityCleansHtmlFromLocation() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => 'Dschungel<script>alert(1)</script>buch',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'location' => 'Dschungelbuch',
        ]);
    }

    public function testPatchActivityTrimsLocation() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => " \t".str_repeat('a', 64)." \t",
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'location' => str_repeat('a', 64),
        ]);
    }
}
