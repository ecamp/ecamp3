<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMultiSelectOptionTest extends ECampApiTestCase {
    protected MultiSelect $defaultMultiSelect;

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->entityClass = MultiSelectOption::class;

        $this->defaultMultiSelect = static::$fixtures['multiSelect1'];
    }

    public function testCreateMethodIsNotAllowed() {
        // when
        $this->create(user: static::$fixtures['user1manager']);

        // then
        $this->assertResponseStatusCodeSame(405);
    }

    // Standard security checks.

    /*
    public function testCreateIsDeniedForAnonymousUser() {
       static::createBasicClient()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);
       $this->assertResponseStatusCodeSame(401);
       $this->assertJsonContains([
           'code' => 401,
           'message' => 'JWT Token not found',
       ]);
    }

    public function testCreateIsDeniedForInvitedCollaborator() {
       // when
       $this->create(user: static::$fixtures['user6invited']);

       // then: throws "item not found" because multiSelect1 is not readable
       $this->assertResponseStatusCodeSame(400);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Item not found for "'.$this->getIriFor($this->defaultMultiSelect).'".',
       ]);
    }

    public function testCreateIsDeniedForInactiveCollaborator() {
       // when
       $this->create(user: static::$fixtures['user5inactive']);

       // then: throws "item not found" because multiSelect1 is not readable
       $this->assertResponseStatusCodeSame(400);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Item not found for "'.$this->getIriFor($this->defaultMultiSelect).'".',
       ]);
    }

    public function testCreateIsDeniedForUnrelatedUser() {
       // when
       $this->create(user: static::$fixtures['user4unrelated']);

       // then: throws "item not found" because multiSelect1 is not readable
       $this->assertResponseStatusCodeSame(400);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Item not found for "'.$this->getIriFor($this->defaultMultiSelect).'".',
       ]);
    }

    public function testCreateIsDeniedForGuest() {
       // when
       $this->create(user: static::$fixtures['user3guest']);

       // then
       $this->assertResponseStatusCodeSame(403);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Access Denied.',
       ]);
    }

    public function testCreateIsDeniedForMember() {
       // when
       $this->create(user: static::$fixtures['user2member']);

       // then
       $this->assertResponseStatusCodeSame(403);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Access Denied.',
       ]);
    }

    public function testCreateIsDeniedForManager() {
       // when
       $response = $this->create(user: static::$fixtures['user1manager']);

       // then
       $this->assertResponseStatusCodeSame(403);
       $this->assertJsonContains([
           'title' => 'An error occurred',
           'detail' => 'Access Denied.',
       ]);
    }*/

    // Payload setup.
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return [];
    }
}
