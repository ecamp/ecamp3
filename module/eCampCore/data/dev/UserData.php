<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\MailAddress;
use eCamp\Core\Entity\User;

class UserData extends AbstractFixture implements DependentFixtureInterface
{
    public static $USER = User::class . ':USER';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(User::class);

        $user = $repository->findOneBy([ 'username' => 'test-user' ]);
        if ($user == null) {
            $mail = new MailAddress();
            $mail->setMail('test@ecamp3.dev');

            $user = new User();
            $user->setUsername('test-user');
            $user->setRole(User::ROLE_USER);
            $user->setTrustedMailAddress($mail);
            $user->setState(User::STATE_ACTIVATED);

            $manager->persist($user);
        }
        $this->addReference(self::$USER, $user);


        $manager->flush();
    }

    function getDependencies() {
        return [ GroupData::class, CampTypeData::class ];
    }
}
