<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityTest extends ECampApiTestCase {
    public function testPatchActivityIsDeniedForAnonymousUser() {
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1campPrototype');
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

    public function testPatchActivityFromSharedCampIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1campShared');
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

    public function testPatchActivityFromSharedCampIsDeniedForInactiveUser() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
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

    public function testPatchActivityFromSharedCampIsDeniedForInvitedUser() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
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

    public function testPatchActivityFromSharedCampIsAllowedForManager() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
            'title' => 'Hello World',
            'location' => 'Stoos',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
            'location' => 'Stoos',
        ]);
    }

    public function testPatchActivityValidatesCategoryFromSameCamp() {
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => null,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'title: This value should not be blank.',
        ]);
    }

    public function testPatchActivityValidatesTitleMinLength() {
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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

    public function testPatchActivityCleansForbiddenCharactersFromTitle() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'title' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => "this is 'a' <sample> text😀 \\",
        ]);
    }

    public function testPatchActivityTrimsTitle() {
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => null,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'location: This value should not be null.',
        ]);
    }

    public function testPatchActivityAllowsSettingLocationToEmptyString() {
        $activity = static::getFixture('activity1');
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
        $activity = static::getFixture('activity1');
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

    public function testPatchActivityCleansForbiddenCharactersFromLocation() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activities/'.$activity->getId(), ['json' => [
                'location' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'location' => "this is 'a' <sample> text😀 \\",
        ]);
    }

    public function testPatchActivityTrimsLocation() {
        $activity = static::getFixture('activity1');
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
