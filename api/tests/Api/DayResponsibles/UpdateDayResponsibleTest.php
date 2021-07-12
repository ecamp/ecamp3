<?php

namespace App\Tests\Api\DayResponsibles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateDayResponsibleTest extends ECampApiTestCase {
    public function testPatchDayResponsibleIsNotAllowed() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials()->request('PATCH', '/day_responsibles/'.$dayResponsible->getId(), ['json' => [
            'day' => $this->getIriFor('day2period1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
