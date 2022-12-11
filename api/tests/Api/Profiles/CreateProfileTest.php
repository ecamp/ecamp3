<?php

namespace App\Tests\Api\Profiles;

use ApiPlatform\Metadata\Post;
use App\Entity\Profile;
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
        return $this->getExamplePayload(Profile::class, Post::class, $attributes, [], $except);
    }
}
