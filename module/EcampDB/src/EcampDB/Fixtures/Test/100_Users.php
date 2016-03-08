<?php
namespace EcampDB\Fixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use EcampCore\Entity\Login;
use EcampCore\Entity\User;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const JOHN = 'user-john';
    const PAULINE = 'user-pauline';
    const TIFFANY = 'user-tiffany';
    const BILL = 'user-bill';



    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'username' => 'john',
                'firstname' => 'John',
                'surname' => 'Smith',
                'scoutname' => 'Johnny',
                'email' => 'john.smith@ecamp3.ch',
                'role' => User::ROLE_USER,
                'state' => User::STATE_ACTIVATED,
                'password' => 'john',
                'reference' => self::JOHN
            ),
            array(
                'username' => 'pauline',
                'firstname' => 'Pauline',
                'surname' => 'Romero',
                'scoutname' => 'Pauli',
                'email' => 'pauline.romero@ecamp3.ch',
                'role' => User::ROLE_USER,
                'state' => User::STATE_ACTIVATED,
                'password' => 'pauline',
                'reference' => self::PAULINE
            ),
            array(
                'username' => 'tiffany',
                'firstname' => 'Tiffany',
                'surname' => 'Cooper',
                'scoutname' => 'Tiff',
                'email' => 'tiffany.cooper@ecamp3.ch',
                'role' => User::ROLE_USER,
                'state' => User::STATE_ACTIVATED,
                'password' => 'tiffany',
                'reference' => self::TIFFANY
            ),
            array(
                'username' => 'bill',
                'firstname' => 'Bill',
                'surname' => 'Graves',
                'scoutname' => 'Billy',
                'email' => 'bill.graves@ecamp3.ch',
                'role' => User::ROLE_USER,
                'state' => User::STATE_ACTIVATED,
                'password' => 'bill',
                'reference' => self::BILL
            )
        ));
    }

    private function load_(ObjectManager $manager, array $config)
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
        return 100;
    }
}
