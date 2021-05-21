<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

    private User $user;

    public function setUp(): void {
        parent::setUp();
        $this->user = (new User())
            ->setUsername('bi-pi')
            ->setFirstname('Robert')
            ->setSurname('Baden-Powell')
            ->setNickname('Bi-Pi');
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
        $this->user->setNickname('');

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('Robert Baden-Powell', $displayName);
    }

    public function testDisplayNameUsesFirstname() {
        // given
        $this->user->setNickname('')->setSurname('');

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('Robert', $displayName);
    }

    public function testDisplayNameUsesUsername() {
        // given
        $this->user->setNickname('')->setFirstname('');

        // when
        $displayName = $this->user->getDisplayName();

        // then
        $this->assertEquals('bi-pi', $displayName);
    }
}