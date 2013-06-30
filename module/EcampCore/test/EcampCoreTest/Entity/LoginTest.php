<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Login;

class LoginTest extends \PHPUnit_Framework_TestCase
{

    public function testCheckPassword()
    {
        $login = new Login();
        $login->setUser(new User());

        $login->setNewPassword('correctPassword');

        $this->assertFalse($login->checkPassword('wrongPassword'));
        $this->assertTrue($login->checkPassword('correctPassword'));
    }

    public function testPasswordReset()
    {
        $login = new Login();
        $login->setUser(new User());

        $pwResetKey = $login->createPwResetKey();
        $this->assertEquals($pwResetKey, $login->getPwResetKey());

        $login->clearPwResetKey();
        $this->assertNull($login->getPwResetKey());
    }

}
