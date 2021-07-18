<?php

namespace App\Tests\Api\Days;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListDaysTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListDaysIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/days');
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
            ['href' => $this->getIriFor('day1period1')],
            ['href' => $this->getIriFor('day2period1')],
            ['href' => $this->getIriFor('day3period1')],
            ['href' => $this->getIriFor('day1period2')],
            ['href' => $this->getIriFor('day1period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialItemsFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials()->request('GET', '/days?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('day1period1')],
            ['href' => $this->getIriFor('day2period1')],
            ['href' => $this->getIriFor('day3period1')],
        ], $response->toArray()['_links']['items']);
    }
}
