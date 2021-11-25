<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option\MultiSelect\Option;

use App\Entity\ContentNode\MultiSelectOption;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadMultiSelectOptionTest extends ECampApiTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->defaultEntity = static::$fixtures['multiSelectOption1'];
    }

    public function testGetOption() {
        // given
        /** @var MultiSelectOption $option */
        $option = $this->defaultEntity;

        // when
        $this->get($option);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $option->getId(),
            'translateKey' => $option->translateKey,
            'checked' => $option->checked,
            'position' => $option->getPosition(),

            '_links' => [
                'multiSelect' => ['href' => $this->getIriFor($option->multiSelect)],
            ],
        ]);
    }

    /**
     * Standard security checks.
     */
    public function testGetIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', "{$this->endpoint}/".$this->defaultEntity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetIsDeniedForInvitedCollaborator() {
        $this->get(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsDeniedForInactiveCollaborator() {
        $this->get(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsDeniedForUnrelatedUser() {
        $this->get(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsAllowedForGuest() {
        $this->get(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetIsAllowedForMember() {
        $this->get(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetIsAllowedForManager() {
        $this->get(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
    }
}
