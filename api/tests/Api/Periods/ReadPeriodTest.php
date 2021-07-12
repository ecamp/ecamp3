<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadPeriodTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSinglePeriodIsAllowedForCollaborator() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('GET', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $period->getId(),
            'description' => $period->description,
            'start' => $period->start->format('Y-m-d'),
            'end' => $period->end->format('Y-m-d'),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?period=/periods/'.$period->getId()],
                'days' => ['href' => '/days?period=/periods/'.$period->getId()],
                'scheduleEntries' => ['href' => '/schedule_entries?period=/periods/'.$period->getId()],
            ],
        ]);
    }
}
