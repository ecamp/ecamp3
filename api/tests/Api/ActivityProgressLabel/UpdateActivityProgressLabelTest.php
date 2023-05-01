<?php

namespace App\Tests\Api\ActivityProgressLabel;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityProgressLabelTest extends ECampApiTestCase {
    public function testPatchActivityProgressLabelIsDeniedForAnonymousUser() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createBasicClient()
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 3,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchActivityProgressLabelIsDeniedForUnrelatedUser() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 3,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchActivityProgressLabelIsDeniedForInactiveCollaborator() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 3,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchActivityProgressLabelIsDeniedForGuest() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 3,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchActivityProgressLabelIsAllowedForMember() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 1,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'position' => 1,
            'title' => 'NewTitle',
            '_links' => [
                'camp' => [
                    'href' => $this->getIriFor('camp1'),
                ],
            ],
        ]);
    }

    public function testPatchActivityProgressLabelIsAllowedForManager() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'position' => 1,
                'title' => 'NewTitle',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'position' => 1,
            'title' => 'NewTitle',
            '_links' => [
                'camp' => [
                    'href' => $this->getIriFor('camp1'),
                ],
            ],
        ]);
    }

    public function testPatchActivityProgressLabelDisallowsChangingCamp() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'camp' => $this->getIriFor('camp2'),
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchActivityProgressLabelValidatesBlankTitle() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
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

    public function testPatchActivityProgressLabelTitleIsTrimmed() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'title' => 'NewTitle  ',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'NewTitle',
            '_links' => [
                'camp' => [
                    'href' => $this->getIriFor('camp1'),
                ],
            ],
        ]);
    }

    public function testPatchActivityProgressLabelValidatesTitleLength() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()
            ->request('PATCH', '/activity_progress_labels/'.$activityProgressLabel->getId(), ['json' => [
                'title' => 'Label 6789 123456789 123456789 12',
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
}
