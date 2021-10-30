<?php

namespace App\Tests\Api\ContentNodes\Storyboard\Section;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateStoryboardSectionTest extends ECampApiTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboard_sections';
        $this->defaultEntity = static::$fixtures['storyboardSection1'];
    }

    public function testPatchCleansHTMLFromText() {
        // given
        $text = ' testText<script>alert(1)</script>';

        // when
        $this->patch($this->defaultEntity, [
            'column1' => $text,
            'column2' => $text,
            'column3' => $text,
        ]);

        // then
        $textSanitized = ' testText';
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'column1' => $textSanitized,
            'column2' => $textSanitized,
            'column3' => $textSanitized,
        ]);
    }

    public function testPatchStoryboardNotAllowed() {
        // when
        $this->patch($this->defaultEntity, [
            'storyboard' => $this->getIriFor(static::$fixtures['storyboard2']),
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Extra attributes are not allowed ("storyboard" is unknown).',
        ]);
    }

    /**
     * Standard security checks.
     */
    public function testPatchIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('PATCH', "{$this->endpoint}/".$this->defaultEntity->getId(), ['json' => [], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchIsDeniedForInvitedCollaborator() {
        $this->patch(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForInactiveCollaborator() {
        $this->patch(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForUnrelatedUser() {
        $this->patch(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchIsDeniedForGuest() {
        $this->patch(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsAllowedForMember() {
        $this->patch(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchIsAllowedForManager() {
        $this->patch(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
    }
}
