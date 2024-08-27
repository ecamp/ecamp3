<?php

namespace App\Tests\Api\Checklists;

use App\Entity\Checklist;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadChecklistTest extends ECampApiTestCase {
    public function testGetSingleChecklistIsDeniedForAnonymousUser() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createBasicClient()->request('GET', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleChecklistIsDeniedForUnrelatedUser() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklists/'.$checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleChecklistIsDeniedForInactiveCollaborator() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/checklists/'.$checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleChecklistIsAllowedForGuest() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/checklists/'.$checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklist->getId(),
            'name' => $checklist->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testGetSingleChecklistIsAllowedForMember() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/checklists/'.$checklist->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklist->getId(),
            'name' => $checklist->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testGetSingleChecklistIsAllowedForManager() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request('GET', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklist->getId(),
            'name' => $checklist->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testGetSingleChecklistFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Checklist $checklist */
        $checklist = static::getFixture('checklist1campPrototype');
        static::createClientWithCredentials()->request('GET', '/checklists/'.$checklist->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $checklist->getId(),
            'name' => $checklist->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('campPrototype')],
            ],
        ]);
    }
}
