<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use ReflectionClass;

/**
 * @internal
 */
class LoginTest extends AbstractTestCase {
    public function testCreateLogin() {
        $user = new User();
        $login = new Login($user, 'test-password');

        $this->assertTrue($login->checkPassword('test-password'));
        $this->assertFalse($login->checkPassword('wrong-password'));
    }

    public function testPwResetKey() {
        $user = new User();
        $login = new Login($user, 'test-password');

        $key = $login->createPwResetKey();
        $this->assertTrue($login->checkPwResetKey($key));

        $login->resetPassword($key, 'new-password');
        $this->assertTrue($login->checkPassword('new-password'));
        $this->assertFalse($login->checkPassword('test-password'));

        $key = $login->createPwResetKey();
        $this->assertFalse($login->checkPwResetKey('wrong-key'));

        $this->expectException(\Exception::class);
        $login->resetPassword('wrong-key', 'newer-password');
    }

    public function testUpdateHashVersion() {
        $password = (new ReflectionClass(Login::class))->getProperty('password');
        $password->setAccessible(true);

        $user = new User();
        $login = new Login($user, 'test-password');

        $key = $login->createPwResetKey();
        $login->resetPassword($key, 'new-password', 0);
        $this->assertTrue($login->checkPassword('new-password'));
        $this->assertEquals('new-password', $password->getValue($login));

        $login->checkPassword('new-password', true);
        $this->assertTrue($login->checkPassword('new-password'));
        $this->assertNotEquals('new-password', $password->getValue($login));
    }
}
