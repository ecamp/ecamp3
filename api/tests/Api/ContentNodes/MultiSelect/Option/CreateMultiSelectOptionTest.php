<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMultiSelectOptionTest extends ECampApiTestCase {
    protected MultiSelect $defaultStorybord;

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->entityClass = MultiSelectOption::class;

        $this->defaultStorybord = static::$fixtures['multiSelect1'];
    }

    public function testCreateCleansHTMLFromText() {
        // given
        $text = ' testText<script>alert(1)</script>';

        // when
        $this->create($this->getExampleWritePayload([
            'translateKey' => $text,
            'checked' => true,
        ]));

        // then
        $textSanitized = ' testText';
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'translateKey' => $textSanitized,
            'checked' => true,
        ]);
    }

    /**
     * Standard security checks.
     */
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
            'detail' => 'Item not found for "'.$this->getIriFor($this->defaultStorybord).'".',
        ]);
    }

    public function testCreateIsDeniedForInactiveCollaborator() {
        // when
        $this->create(user: static::$fixtures['user5inactive']);

        // then: throws "item not found" because multiSelect1 is not readable
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor($this->defaultStorybord).'".',
        ]);
    }

    public function testCreateIsDeniedForUnrelatedUser() {
        // when
        $this->create(user: static::$fixtures['user4unrelated']);

        // then: throws "item not found" because multiSelect1 is not readable
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor($this->defaultStorybord).'".',
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

    public function testCreateIsAllowedForMember() {
        $this->create(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateIsAllowedForManager() {
        // when
        $response = $this->create(user: static::$fixtures['user1manager']);
        $id = $response->toArray()['id'];
        $newMultiSelectOption = $this->getEntityManager()->getRepository($this->entityClass)->find($id);

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload($newMultiSelectOption), true);
    }

    /**
     * Payload setup.
     */
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'multiSelect' => $this->getIriFor($this->defaultStorybord),
                'translateKey' => 'key',
                'checked' => true,
            ], $attributes),
            $except
        );
    }

    protected function getExampleReadPayload(MultiSelectOption $self, $attributes = [], $except = []) {
        /** @var MultiSelect $multiSelect */
        $multiSelect = $this->defaultStorybord;

        return [
            'translateKey' => 'key',
            'checked' => true,
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor($self),
                ],
                'multiSelect' => [
                    'href' => $this->getIriFor($multiSelect),
                ],
            ],
        ];
    }
}
