<?php

namespace App\Tests\Api\Days;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteDayTest extends ECampApiTestCase {
    public function testDeleteDayIsNotAllowed() {
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials()->request('DELETE', '/days/'.$day->getId());

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
