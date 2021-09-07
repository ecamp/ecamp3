<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityResponsibleTest extends ECampApiTestCase {
    public function testDeleteActivityResponsibleIsDeniedForAnonymousUser() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClient()->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteActivityResponsibleIsDeniedForUnrelatedUser() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityResponsibleIsDeniedForInactiveCollaborator() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityResponsibleIsDeniedForGuest() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityResponsibleIsAllowedForMember() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ActivityResponsible::class)->find($activityResponsible->getId()));
    }

    public function testDeleteActivityResponsibleIsAllowedForManager() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials()->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ActivityResponsible::class)->find($activityResponsible->getId()));
    }

    public function testDeleteActivityResponsibleFromCampPrototypeIsDeniedForUnrelatedUser() {
        $activityResponsible = static::$fixtures['activityResponsible1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
