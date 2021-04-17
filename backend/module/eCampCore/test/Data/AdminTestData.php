<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;

class AdminTestData extends AbstractFixture {
    public static $ADMIN = User::class.':ADMIN';

    public function load(ObjectManager $manager): void {
        $user = new User();
        $user->setUsername('admin');
        $user->setRole(User::ROLE_ADMIN);
        $user->setState(User::STATE_ACTIVATED);
        $login = new Login($user, 'admin');

        $manager->persist($user);
        $manager->persist($login);

        $manager->flush();

        $this->addReference(self::$ADMIN, $user);
    }
}
