<?php

namespace App\Tests\Api\Profiles;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateProfileTest extends ECampApiTestCase {
    public function testCreateProfileIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/profiles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(User::class, '/profiles', 'post', $attributes, [], $except);
    }
}
