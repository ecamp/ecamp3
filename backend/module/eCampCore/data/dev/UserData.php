<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;

class UserData extends AbstractFixture implements DependentFixtureInterface {
    public static $USER = User::class.':USER';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(User::class);

        $user = $repository->findOneBy(['username' => 'test-user']);
        if (null == $user) {
            $user = new User();
            $user->setUsername('test-user');
            $user->setRole(User::ROLE_USER);
            $user->setTrustedMailAddress('test@ecamp3.dev');
            $user->setState(User::STATE_ACTIVATED);

            $login = new Login($user, 'test');

            $manager->persist($user);
            $manager->persist($login);
        }
        $this->addReference(self::$USER, $user);

        // add a second user
        $user = $repository->findOneBy(['username' => 'mogli']);
        if (null == $user) {
            $user = new User();
            $user->setUsername('mogli');
            $user->setRole(User::ROLE_USER);
            $user->setTrustedMailAddress('mogli@ecamp3.dev');
            $user->setState(User::STATE_ACTIVATED);

            $login = new Login($user, 'test');

            $manager->persist($user);
            $manager->persist($login);
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [CampTypeData::class];
    }
}
