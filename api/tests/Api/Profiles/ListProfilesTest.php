<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListProfilesTest extends ECampApiTestCase {
    public function testListProfilesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testListProfilesIsDeniedForLoggedInUser() {
        static::createClientWithCredentials()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(403);
    }
}
