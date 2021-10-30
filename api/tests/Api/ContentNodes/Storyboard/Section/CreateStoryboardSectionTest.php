<?php

namespace App\Tests\Api\ContentNodes\Storyboard\Section;

use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateStoryboardSectionTest extends ECampApiTestCase {
    protected Storyboard $defaultStorybord;

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboard_sections';
        $this->entityClass = StoryboardSection::class;

        $this->defaultStorybord = static::$fixtures['storyboard1'];
    }

    public function testCreateCleansHTMLFromText() {
        // given
        $text = ' testText<script>alert(1)</script>';

        // when
        $this->create($this->getExampleWritePayload([
            'column1' => $text,
            'column2' => $text,
            'column3' => $text,
        ]));

        // then
        $textSanitized = ' testText';
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'column1' => $textSanitized,
            'column2' => $textSanitized,
            'column3' => $textSanitized,
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

        // then: throws "item not found" because storyboard1 is not readable
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor($this->defaultStorybord).'".',
        ]);
    }

    public function testCreateIsDeniedForInactiveCollaborator() {
        // when
        $this->create(user: static::$fixtures['user5inactive']);

        // then: throws "item not found" because storyboard1 is not readable
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor($this->defaultStorybord).'".',
        ]);
    }

    public function testCreateIsDeniedForUnrelatedUser() {
        // when
        $this->create(user: static::$fixtures['user4unrelated']);

        // then: throws "item not found" because storyboard1 is not readable
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
        $newStoryboardSection = $this->getEntityManager()->getRepository($this->entityClass)->find($id);

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload($newStoryboardSection), true);
    }

    /**
     * Payload setup.
     */
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'storyboard' => $this->getIriFor($this->defaultStorybord),
                'column1' => 'Column 1',
                'column2' => 'Column 2',
                'column3' => 'Column 3',
            ], $attributes),
            $except
        );
    }

    protected function getExampleReadPayload(StoryboardSection $self, $attributes = [], $except = []) {
        /** @var Storyboard $storyboard */
        $storyboard = $this->defaultStorybord;

        return [
            'column1' => 'Column 1',
            'column2' => 'Column 2',
            'column3' => 'Column 3',
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor($self),
                ],

                'storyboard' => [
                    'href' => $this->getIriFor($storyboard),
                ],
            ],
        ];
    }
}
