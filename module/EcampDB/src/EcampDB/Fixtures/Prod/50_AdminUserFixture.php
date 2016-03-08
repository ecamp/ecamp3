<?php

namespace EcampDB\Fixtures\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\Login;
use EcampCore\Entity\User;

class AdminUserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const ADMIN = 'user-admin';

    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'username' => 'admin',
                'firstname' => 'Admin',
                'surname' => 'Administrator',
                'scoutname' => 'Admin',
                'email' => 'admin@ecamp3.ch',
                'role' => User::ROLE_ADMIN,
                'state' => User::STATE_ACTIVATED,
                'password' => 'admin',
                'reference' => self::ADMIN
            )
        ));
    }

    private function load_(ObjectManager$manager, array $config)
    {
        $userRepo = $manager->getRepository('EcampCore\Entity\User');

        foreach ($config as $userConfig) {
            $username = $userConfig['username'];
            $firstname = $userConfig['firstname'];
            $surname = $userConfig['surname'];
            $scoutname = $userConfig['scoutname'];
            $email = $userConfig['email'];
            $role = $userConfig['role'];
            $state = $userConfig['state'];
            $reference = $userConfig['reference'];

            $user = $userRepo->findOneBy(array('username' => $username));

            if($user == null){
                $user = new User();
                $user->setUsername($username);
                $manager->persist($user);
            }

            $user->setFirstname($firstname);
            $user->setSurname($surname);
            $user->setScoutname($scoutname);
            $user->setEmail($email);
            $user->setRole($role);
            $user->setState($state);

            if(array_key_exists('password', $userConfig)){
                $password = $userConfig['password'];
                $login = $user->getLogin();

                if($login == null){
                    $login = new Login($user, $password);
                    $manager->persist($login);
                } else {
                    $key = $login->createPwResetKey();
                    $login->resetPassword($key, $password);
                }
            }

            $this->addReference($reference, $user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }
}