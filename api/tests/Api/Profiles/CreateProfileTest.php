<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateProfileTest extends ECampApiTestCase {
    public function testCreateProfileIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/profiles', ['json' => []]);

        $this->assertResponseStatusCodeSame(405);
    }
}
