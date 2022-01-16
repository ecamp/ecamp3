<?php

namespace App\Tests\Api\Profiles;

use ApiPlatform\Core\Api\OperationType;
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
        return $this->getExamplePayload(User::class, OperationType::COLLECTION, 'post', $attributes, [], $except);
    }
}
