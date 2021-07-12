<?php

namespace App\Tests\Api\Periods;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListPeriodsTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListPeriodsIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/periods');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 3,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
            ['href' => $this->getIriFor('period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=/camps/'.$camp->getId());
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
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
        ], $response->toArray()['_links']['items']);
    }
}
