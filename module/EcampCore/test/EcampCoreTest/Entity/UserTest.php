<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Image;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public static function createUser()
    {
        $user = new User();
        $user->setUsername("User.Username");
        $user->setFirstname("User.Firstname");
        $user->setSurname("User.Surname");
        $user->setScoutname("User.Scoutname");
        $user->setStreet("User.Street");
        $user->setZipcode("User.Zipcode");
        $user->setCity("User.City");
        $user->setEmail('User@Email.com');
        $user->setHomeNr('000 000 00 00');
        $user->setMobilNr('111 111 11 11');
        $user->setGender(User::GENDER_MALE);

        return $user;
    }

    public function testNaming()
    {
        $user = new User();

        $user->setUsername('username');
        $user->setFirstname('firstname');
        $user->setSurname('surname');

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('firstname', $user->getFirstname());
        $this->assertEquals('surname', $user->getSurname());

        $this->assertEquals('firstname surname', $user->getDisplayName());
        $this->assertEquals('firstname surname', $user->getFullName());

        $user->setScoutname('scoutname');
        $this->assertEquals('scoutname', $user->getScoutname());
        $this->assertEquals('scoutname', $user->getDisplayName());
        $this->assertEquals('scoutname, firstname surname', $user->getFullName());
    }

    public function testAdress()
    {
        $user = new User();

        $user->setStreet('Any Street');
        $user->setCity('Any City');
        $user->setZipcode('Any ZIP');

        $this->assertEquals('Any Street', $user->getStreet());
        $this->assertEquals('Any City', $user->getCity());
        $this->assertEquals('Any ZIP', $user->getZipcode());
    }

    public function testContacts()
    {
        $user = new User();

        $user->setEmail('any@mail.com');
        $user->setHomeNr('000 000 00 00');
        $user->setMobilNr('111 111 11 11');

        $this->assertEquals('any@mail.com', $user->getEmail());
        $this->assertEquals('000 000 00 00', $user->getHomeNr());
        $this->assertEquals('111 111 11 11', $user->getMobilNr());
    }

    public function testGender()
    {
        $user = new User();

        $this->assertFalse($user->isMale());
        $this->assertFalse($user->isFemale());

        $user->setGender(User::GENDER_MALE);
        $this->assertEquals(User::GENDER_MALE, $user->getGender());
        $this->assertTrue($user->isMale());
        $this->assertFalse($user->isFemale());

        $user->setGender(User::GENDER_FEMALE);
        $this->assertEquals(User::GENDER_FEMALE, $user->getGender());
        $this->assertFalse($user->isMale());
        $this->assertTrue($user->isFemale());

    }

    public function testUserActivation()
    {
        $user = new User();
        $user->setState(User::STATE_REGISTERED);

        $user->resetActivationCode();
        $this->assertFalse($user->checkEmailVerificationCode(""));

        $acode = $user->createNewActivationCode();

        $this->assertTrue($user->checkEmailVerificationCode($acode));
        $this->assertFalse($user->checkEmailVerificationCode(""));

        $this->assertFalse($user->activateUser(""));
        $this->assertEquals(User::STATE_REGISTERED, $user->getState());

        $this->assertTrue($user->activateUser($acode));
        $this->assertEquals(User::STATE_ACTIVATED, $user->getState());
    }

    public function testImages()
    {
        $user = new User();
        $image = new Image();

        $user->setImage($image);
        $this->assertEquals($image, $user->getImage());

        $user->delImage();
        $this->assertNull($user->getImage());

    }

    public function testRole()
    {
        $user = new User();

        $this->assertEquals(User::ROLE_USER, $user->getRole());
        $this->assertEquals(User::ROLE_USER, $user->getRoleId());

        $user->setRole(User::ROLE_ADMIN);
        $this->assertEquals(User::ROLE_ADMIN, $user->getRole());
        $this->assertEquals(User::ROLE_ADMIN, $user->getRoleId());

    }

    public function testBirthday()
    {
        $user = new User();
        $birthday = date('now');

        $user->setBirthday($birthday);
        $this->assertEquals($birthday, $user->getBirthday());
    }

}
