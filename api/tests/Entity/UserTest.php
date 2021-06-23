<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class UserTest extends TestCase {
    private User $user;

    public function setUp(): void {
        parent::setUp();
        $this->user = new User();
        $this->user->username = 'bi-pi';
        $this->user->firstname = 'Robert';
        $this->user->surname = 'Baden-Powell';
        $this->user->nickname = 'Bi-Pi';
    }

    public function testDisplayNameUsesNicknameIfPresent() {
        // given

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('Bi-Pi', $displayName);
    }

    public function testDisplayNameUsesFullName() {
        // given
        $this->user->nickname = '';

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('Robert Baden-Powell', $displayName);
    }

    public function testDisplayNameUsesFirstname() {
        // given
        $this->user->nickname = '';
        $this->user->surname = '';

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('Robert', $displayName);
    }

    public function testDisplayNameUsesUsername() {
        // given
        $this->user->nickname = '';
        $this->user->firstname = '';

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('bi-pi', $displayName);
    }
}
