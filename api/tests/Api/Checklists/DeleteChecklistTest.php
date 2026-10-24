<?php

namespace App\Tests\Api\Checklists;

use App\Entity\Checklist;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteChecklistTest extends ECampApiTestCase {
    // Prototype-Checklist

    public function testDeletePrototypeChecklistIsDeniedForAnonymousUser() {
        $checklist = static::getFixture('checklistPrototype');
        static::createBasicClient()->request('DELETE', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeletePrototypeChecklistIsDeniedForUser() {
        $checklist = static::getFixture('checklistPrototype');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeletePrototypeChecklistIsAllowedForAdmin() {
        $checklist = static::getFixture('checklistPrototype');
        static::createClientWithCredentials(['email' => static::$fixtures['admin']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Checklist::class)->find($checklist->getId()));
    }

    // Camp-Checklist

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
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistIsDeniedForInactiveCollaborator() {
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteChecklistIsDeniedForGuest() {
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistIsAllowedForMember() {
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Checklist::class)->find($checklist->getId()));
    }

    public function testDeleteChecklistIsAllowedForManager() {
        $checklist = static::getFixture('checklist2WithNoItems');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Checklist::class)->find($checklist->getId()));
    }

    public function testDeleteChecklistFromCampPrototypeIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$checklist->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistFromSharedCampIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1campShared');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$checklist->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistFromSharedCampIsDeniedForInactiveUser() {
        $checklist = static::getFixture('checklist1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistFromSharedCampIsDeniedForInvitedUser() {
        $checklist = static::getFixture('checklist1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('DELETE', '/checklists/'.$checklist->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteChecklistIsDeniedWhenUsedInChecklistNode() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request('DELETE', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'checklistItems[0].checklistNodes: It\'s not possible to delete a checklist item as long as checklist nodes are referencing it.',
        ]);
    }
}
