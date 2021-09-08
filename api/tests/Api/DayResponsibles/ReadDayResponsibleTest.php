<?php

namespace App\Tests\Api\DayResponsibles;

use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadDayResponsibleTest extends ECampApiTestCase {
    public function testGetSingleDayResponsibleIsDeniedForAnonymousUser() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createBasicClient()->request('GET', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleDayResponsibleIsDeniedForUnrelatedUser() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/day_responsibles/'.$dayResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleDayResponsibleIsDeniedForInactiveCollaborator() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/day_responsibles/'.$dayResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleDayResponsibleIsAllowedForGuest() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/day_responsibles/'.$dayResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $dayResponsible->getId(),
            '_links' => [
                'day' => ['href' => $this->getIriFor('day1period1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleDayResponsibleIsAllowedForMember() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/day_responsibles/'.$dayResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $dayResponsible->getId(),
            '_links' => [
                'day' => ['href' => $this->getIriFor('day1period1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleDayResponsibleIsAllowedForManager() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials()->request('GET', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $dayResponsible->getId(),
            '_links' => [
                'day' => ['href' => $this->getIriFor('day1period1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }

    public function testGetSingleDayResponsibleInCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1day1period1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $dayResponsible->getId(),
            '_links' => [
                'day' => ['href' => $this->getIriFor('day1period1campPrototype')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1campPrototype')],
            ],
        ]);
    }
}
