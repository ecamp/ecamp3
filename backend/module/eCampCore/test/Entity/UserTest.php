<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class UserTest extends AbstractTestCase {
    public function testUserNonRegistered() {
        $user = new User();
        $user->setUsername('username');
        $user->setMailAddress('test@eCamp3.ch');

        $this->assertEquals(User::STATE_NONREGISTERED, $user->getState());
        $this->assertEquals(User::ROLE_GUEST, $user->getRole());
        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('test@eCamp3.ch', $user->getUntrustedMailAddress());
    }

    public function testUserActivated() {
        $user = new User();
        $user->setState(User::STATE_REGISTERED);
        $user->setRole(User::ROLE_USER);
        $user->setUsername('username');
        $key = $user->setMailAddress('test@eCamp3.ch');

        $this->assertEquals(User::STATE_REGISTERED, $user->getState());
        $this->assertEquals(User::ROLE_USER, $user->getRole());

        $verified = $user->verifyMailAddress('');
        $this->assertFalse($verified);

        $verified = $user->verifyMailAddress($key);
        $this->assertTrue($verified);

        $this->assertEquals(User::STATE_ACTIVATED, $user->getState());
        $this->assertEmpty($user->getUntrustedMailAddress());
        $this->assertEquals('test@eCamp3.ch', $user->getTrustedMailAddress());

        $key = $user->setMailAddress('test@eCamp3.ch');
        $this->assertEmpty($key);
        $this->assertEmpty($user->getUntrustedMailAddress());
        $this->assertNotEmpty($user->getTrustedMailAddress());
    }
}
