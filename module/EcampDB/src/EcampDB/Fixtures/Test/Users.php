<?php
namespace EcampDB\Fixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use EcampCore\Entity\Login;
use EcampCore\Entity\User;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class Users extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('john');
        $user->setFirstname('John');
        $user->setSurname('Smith');
        $user->setState(User::STATE_ACTIVATED);

        $login = new Login($user);
        $login->setNewPassword('john');

        $manager->persist($user);
        $manager->persist($login);
        $manager->flush();

        $this->addReference('user1', $user);
    }

    public function getOrder()
    {
        return 10;
    }
}
