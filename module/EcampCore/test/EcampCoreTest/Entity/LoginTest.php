<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Login;

class LoginTest extends \PHPUnit_Framework_TestCase
{

    public function testCheckPassword()
    {
        $user = new User();
        $login = new Login($user);

        $login->setNewPassword('correctPassword');

        $this->assertEquals($user, $login->getUser());
        $this->assertFalse($login->checkPassword('wrongPassword'));
        $this->assertTrue($login->checkPassword('correctPassword'));
    }

    public function testPasswordReset()
    {
        $user = new User();
        $login = new Login($user);

        $pwResetKey = $login->createPwResetKey();
        $this->assertEquals($pwResetKey, $login->getPwResetKey());

        $login->clearPwResetKey();
        $this->assertNull($login->getPwResetKey());
    }

}
