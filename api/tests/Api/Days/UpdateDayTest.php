<?php

namespace App\Tests\Api\Days;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateDayTest extends ECampApiTestCase {
    public function testPatchDayIsNotAllowed() {
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials()->request('PATCH', '/days/'.$day->getId(), ['json' => [
            'dayOffset' => 3,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
