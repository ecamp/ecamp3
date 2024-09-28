<?php

namespace App\Tests\Api\Checklists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListChecklistTest extends ECampApiTestCase {
    public function testListChecklistsIsDeniedForAnonymousUser() {
        $response = static::createBasicClient()->request('GET', '/checklists');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListChecklistsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a checklist that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['checklist1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/checklists');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistPrototype')],
            ['href' => $this->getIriFor('checklist1')],
            ['href' => $this->getIriFor('checklist2WithNoItems')],
            ['href' => $this->getIriFor('checklist1camp2')],
            ['href' => $this->getIriFor('checklist1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/checklists?camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklist1')],
            ['href' => $this->getIriFor('checklist2WithNoItems')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/checklists?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListChecklistsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/checklists?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListChecklistsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/checklists?camp=%2Fcamps%2F'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklist1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistsAsCampSubresourceIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId().'/checklists');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklist1')],
            ['href' => $this->getIriFor('checklist2WithNoItems')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListChecklistsAsCampSubresourceIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/camps/'.$camp->getId().'/checklists')
        ;

        $this->assertResponseStatusCodeSame(404);
    }
}
