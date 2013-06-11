<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testDisplayName()
    {
        $user = new User();

        $user->setFirstname('firstname');
        $user->setSurname('surname');
        $this->assertEquals('firstname surname', $user->getDisplayName());

        $user->setScoutname('scoutname');
        $this->assertEquals('scoutname', $user->getDisplayName());
    }

    public function testFullName()
    {
        $user = new User();

        $user->setFirstname('firstname');
        $user->setSurname('surname');
        $this->assertEquals('firstname surname', $user->getFullName());

        $user->setScoutname('scoutname');
        $this->assertEquals('scoutname, firstname surname', $user->getFullName());
    }

    public function testGender()
    {
        $user = new User();

        $this->assertFalse($user->isMale());
        $this->assertFalse($user->isFemale());

        $user->setGender(User::GENDER_MALE);
        $this->assertTrue($user->isMale());
        $this->assertFalse($user->isFemale());

        $user->setGender(User::GENDER_FEMALE);
        $this->assertFalse($user->isMale());
        $this->assertTrue($user->isFemale());

    }

    public function testUserActivation()
    {
        $user = new User();
        $user->setState(User::STATE_REGISTERED);

        $user->resetActivationCode();
        $this->assertFalse($user->checkActivationCode(""));

        $acode = $user->createNewActivationCode();

        $this->assertTrue($user->checkActivationCode($acode));
        $this->assertFalse($user->checkActivationCode(""));

        $this->assertFalse($user->activateUser(""));
        $this->assertEquals(User::STATE_REGISTERED, $user->getState());

        $this->assertTrue($user->activateUser($acode));
        $this->assertEquals(User::STATE_ACTIVATED, $user->getState());

    }

}
