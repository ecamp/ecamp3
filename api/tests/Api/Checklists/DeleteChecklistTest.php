<?php

namespace App\Tests\Api\Checklists;

use App\Entity\Checklist;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteChecklistTest extends ECampApiTestCase {
    public function testDeleteChecklistIsDeniedForAnonymousUser() {
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createBasicClient()->request('DELETE', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteChecklistIsDeniedForUnrelatedUser() {
        $Checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/checklists/'.$Checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistIsDeniedForInactiveCollaborator() {
        $Checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/checklists/'.$Checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistIsDeniedForGuest() {
        $Checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/checklists/'.$Checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistIsAllowedForMember() {
        $Checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/checklists/'.$Checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Checklist::class)->find($Checklist->getId()));
    }

    public function testDeleteChecklistIsAllowedForManager() {
        $Checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$Checklist->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Checklist::class)->find($Checklist->getId()));
    }

    public function testDeleteChecklistFromCampPrototypeIsDeniedForUnrelatedUser() {
        $Checklist = static::getFixture('checklist1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$Checklist->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
