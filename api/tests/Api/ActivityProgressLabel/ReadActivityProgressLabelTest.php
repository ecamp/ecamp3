<?php

namespace App\Tests\Api\ActivityProgressLabel;

use App\Entity\ActivityProgressLabel;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadActivityProgressLabelTest extends ECampApiTestCase {
    public function testGetSingleActivityProgressLabelIsDeniedForAnonymousUser() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createBasicClient()
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleActivityProgressLabelIsDeniedForUnrelatedUser() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityProgressLabelIsDeniedForInactiveCollaborator() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityProgressLabelIsAllowedForGuest() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityProgressLabel->getId(),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testGetSingleActivityProgressLabelIsAllowedForMember() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityProgressLabel->getId(),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testGetSingleActivityProgressLabelIsAllowedForManager() {
        /** @var ActivityProgressLabel $activityProgressLabel */
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()
            ->request('GET', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityProgressLabel->getId(),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }
}
