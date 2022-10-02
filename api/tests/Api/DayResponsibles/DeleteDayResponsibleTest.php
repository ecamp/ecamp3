<?php

namespace App\Tests\Api\DayResponsibles;

use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteDayResponsibleTest extends ECampApiTestCase {
    public function testDeleteDayResponsibleIsDeniedForAnonymousUser() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createBasicClient()->request('DELETE', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteDayResponsibleIsDeniedForUnrelatedUser() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/day_responsibles/'.$dayResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteDayResponsibleIsDeniedForInactiveCollaborator() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/day_responsibles/'.$dayResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteDayResponsibleIsDeniedForGuest() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/day_responsibles/'.$dayResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteDayResponsibleIsAllowedForMember() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/day_responsibles/'.$dayResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(DayResponsible::class)->find($dayResponsible->getId()));
    }

    public function testDeleteDayResponsibleIsAllowedForManager() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials()->request('DELETE', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(DayResponsible::class)->find($dayResponsible->getId()));
    }

    public function testDeleteDayResponsibleInCampPrototypeIsDeniedForUnrelatedUser() {
        $dayResponsible = static::$fixtures['dayResponsible1day1period1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/day_responsibles/'.$dayResponsible->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
