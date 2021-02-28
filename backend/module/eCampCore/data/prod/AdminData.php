<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;

class AdminData extends AbstractFixture {
    public static $ADMIN = User::class.':ADMIN';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(User::class);

        /** @var User $admin */
        $admin = $repository->findOneBy(['username' => 'admin']);
        if (null == $admin) {
            $admin = new User();
            $admin->setUsername('admin');
            $admin->setRole(User::ROLE_ADMIN);
            $admin->setState(User::STATE_ACTIVATED);

            $login = new Login($admin, 'admin');

            $manager->persist($admin);
            $manager->persist($login);
        }
        $this->addReference(self::$ADMIN, $admin);

        $manager->flush();
    }
}
