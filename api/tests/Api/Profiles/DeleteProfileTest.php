<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteProfileTest extends ECampApiTestCase {
    public function testDeleteProfileIsDeniedForAnonymousUser() {
        $user = static::getFixture('user1manager');
        static::createBasicClient()->request('DELETE', '/profiles/'.$user->getId());
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteProfileIsDeniedForDifferentProfile() {
        $user2 = static::getFixture('user2member');
        static::createClientWithCredentials()->request('DELETE', '/profiles/'.$user2->getId());
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteProfileIsDeniedForSelf() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('DELETE', '/profiles/'.$user->getId());
        $this->assertResponseStatusCodeSame(405);
    }
}
