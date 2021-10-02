<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadActivityResponsibleTest extends ECampApiTestCase {
    public function testGetSingleActivityResponsibleIsDeniedForAnonymousUser() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createBasicClient()->request('GET', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleActivityResponsibleIsDeniedForUnrelatedUser() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/activity_responsibles/'.$activityResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityResponsibleIsDeniedForInactiveCollaborator() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/activity_responsibles/'.$activityResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityResponsibleIsAllowedForGuest() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/activity_responsibles/'.$activityResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityResponsible->getId(),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleActivityResponsibleIsAllowedForMember() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/activity_responsibles/'.$activityResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityResponsible->getId(),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleActivityResponsibleIsAllowedForManager() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials()->request('GET', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityResponsible->getId(),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleActivityResponsibleFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityResponsible->getId(),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1campPrototype')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1campPrototype')],
            ],
        ]);
    }
}
