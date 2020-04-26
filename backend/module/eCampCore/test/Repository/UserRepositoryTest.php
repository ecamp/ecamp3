<?php

namespace eCamp\CoreTest\Repository;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Core\Repository\UserRepository;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;
use Hybridauth\User\Profile;

/**
 * @internal
 */
class UserRepositoryTest extends AbstractDatabaseTestCase {
    public function testFindByUsername() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        /** @var EntityManager $entityManager */
        $entityManager = \eCampApp::GetService(EntityManager::class);

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $userService->create((object) [
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch',
        ]);
        $entityManager->flush();

        $user = $userRepository->findByUsername('username');
        $this->assertNotEmpty($user);

        $user = $userRepository->findByUsername('hjkdjhheug');
        $this->assertEmpty($user);
    }

    public function testFindByMail() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        /** @var EntityManager $entityManager */
        $entityManager = \eCampApp::GetService(EntityManager::class);

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $userService->create((object) [
            'username' => 'username1',
            'mailAddress' => 'test1@eCamp3.ch',
        ]);

        $profile = new Profile();
        $profile->displayName = 'username2';
        $profile->email = 'test2@eCamp3.ch';
        $userService->create($profile);

        $entityManager->flush();

        $user = $userRepository->findByMail('test1@eCamp3.ch');
        $this->assertNotEmpty($user);

        $user = $userRepository->findByMail('test2@eCamp3.ch');
        $this->assertNotEmpty($user);

        $user = $userRepository->findByMail('hjkdjhheug@eCamp3.ch');
        $this->assertEmpty($user);
    }
}
