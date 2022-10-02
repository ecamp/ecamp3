<?php

namespace App\Tests\Entity;

use App\Entity\Profile;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProfileTest extends TestCase {
    private Profile $profile;

    public function setUp(): void {
        parent::setUp();
        $this->profile = new Profile();
        $this->profile->firstname = 'Robert';
        $this->profile->surname = 'Baden-Powell';
        $this->profile->nickname = 'Bi-Pi';
        $this->profile->email = 'test@test.com';
    }

    public function testDisplayNameUsesNicknameIfPresent() {
        // given

        // when
        $displayName = $this->profile->getDisplayName();

        // then
        $this->assertEquals('Bi-Pi', $displayName);
    }

    public function testDisplayNameUsesFullName() {
        // given
        $this->profile->nickname = '';

        // when
        $displayName = $this->profile->getDisplayName();

        // then
        $this->assertEquals('Robert Baden-Powell', $displayName);
    }

    public function testDisplayNameUsesFirstname() {
        // given
        $this->profile->nickname = '';
        $this->profile->surname = '';

        // when
        $displayName = $this->profile->getDisplayName();

        // then
        $this->assertEquals('Robert', $displayName);
    }

    public function testDisplayNameUsesEmailHashAsFallback() {
        // given
        $this->profile->nickname = '';
        $this->profile->firstname = '';

        // when
        $displayName = $this->profile->getDisplayName();

        // then
        $this->assertEquals('Noname-b642', $displayName);
    }
}
