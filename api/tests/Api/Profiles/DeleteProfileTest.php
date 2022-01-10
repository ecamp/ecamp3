<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteProfileTest extends ECampApiTestCase {
    public function testDeleteProfileIsDeniedForAnonymousUser() {
        $user = static::$fixtures['user1manager'];
        static::createBasicClient()->request('DELETE', '/profiles/'.$user->getId());
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteProfileIsDeniedForDifferentProfile() {
        $user2 = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('DELETE', '/profiles/'.$user2->getId());
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteProfileIsDeniedForSelf() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('DELETE', '/profiles/'.$user->getId());
        $this->assertResponseStatusCodeSame(405);
    }
}
