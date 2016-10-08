<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Login;

class LoginTest extends \PHPUnit_Framework_TestCase
{

    public function testCheckPassword()
    {
        $user = new User();
        $login = new Login($user, 'correctPassword');

        $this->assertEquals($user, $login->getUser());
        $this->assertFalse($login->checkPassword('wrongPassword'));
        $this->assertTrue($login->checkPassword('correctPassword'));
    }

    public function testPasswordReset()
    {
        $user = new User();
        $login = new Login($user, 'a');
        $this->assertTrue($login->checkPassword('a'));

        $pwResetKey = $login->createPwResetKey();
        $login->resetPassword($pwResetKey, 'b');
        $this->assertFalse($login->checkPassword('a'));
        $this->assertTrue($login->checkPassword('b'));
    }

    public function testPasswordChange()
    {
        $user = new User();
        $login = new Login($user, 'a');
        $this->assertTrue($login->checkPassword('a'));

        $login->changePassword('a', 'b');
        $this->assertFalse($login->checkPassword('a'));
        $this->assertTrue($login->checkPassword('b'));
    }

    public function testPasswordRehash()
    {
        $user = new User();
        $login = new Login($user, 'a');
        $this->assertTrue($login->checkPassword('a'));

        $salt1 = $this->getSalt($login);
        $login->checkPassword('a', true);
        $this->assertTrue($login->checkPassword('a'));

        $salt2 = $this->getSalt($login);

        $this->assertNotEquals($salt1, $salt2);
    }

    private function getSalt(Login $login){
        $myClassReflection = new \ReflectionClass(get_class($login));
        $salt = $myClassReflection->getProperty('salt');
        $salt->setAccessible(true);
        return $salt->getValue($login);
    }


    public function testHashVersion()
    {
        $user = new User();
        $login = new Login($user, 'a');
        $this->assertTrue($login->checkPassword('a'));

        $login->changePassword('a', 'a', 0);
        $this->assertEquals(0, $login->getHashVersion());
        $this->assertTrue($login->checkPassword('a'));

        $login->changePassword('a', 'a', 1);
        $this->assertEquals(1, $login->getHashVersion());
        $this->assertTrue($login->checkPassword('a'));
    }

}
