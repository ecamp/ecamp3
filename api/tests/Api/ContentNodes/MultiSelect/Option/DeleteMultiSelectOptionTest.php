<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMultiSelectOptionTest extends ECampApiTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->defaultEntity = static::$fixtures['multiSelectOption1'];
    }

    public function testDeleteIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('DELETE', "{$this->endpoint}/".$this->defaultEntity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);

        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForInvitedCollaborator() {
        $this->delete(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForInactiveCollaborator() {
        $this->delete(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForUnrelatedUser() {
        $this->delete(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForGuest() {
        $this->delete(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForMember() {
        $this->delete(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(403);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForManager() {
        $this->delete(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(403);
        $this->assertEntityStillExists();
    }
}
