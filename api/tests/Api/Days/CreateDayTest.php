<?php

namespace App\Tests\Api\Days;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateDayTest extends ECampApiTestCase {
    public function testCreateDayIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/days', ['json' => [
            'name' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
