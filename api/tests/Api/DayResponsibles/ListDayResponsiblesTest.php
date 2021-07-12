<?php

namespace App\Tests\Api\DayResponsibles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListDayResponsiblesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListDayResponsiblesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles');
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
            ['href' => $this->getIriFor('dayResponsible1')],
            ['href' => $this->getIriFor('dayResponsible1day2period1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByDayIsAllowedForCollaborator() {
        $day = static::$fixtures['day1period1'];
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('dayResponsible1')],
        ], $response->toArray()['_links']['items']);
    }
}
