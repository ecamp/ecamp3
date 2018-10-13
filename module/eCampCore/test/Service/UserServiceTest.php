<?php

namespace eCamp\CoreTest\Service;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;

class UserServiceTest extends AbstractDatabaseTestCase {
    public function testCreateUser() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        $user = $userService->create((object)[
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch'
        ]);

        $this->assertEquals(User::STATE_NONREGISTERED, $user->getState());
    }


    public function testGetUser() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        $user = $userService->create((object)[
            'username' => 'username2',
            'mailAddress' => 'test2@eCamp3.ch'
        ]);

        $this->getEntityManager()->flush();

        $userId = $user->getId();
        $this->login($userId);

        $user2 = $userService->fetch($userId);
        $this->assertEquals($user, $user2);

        $user3 = $userService->fetch(-1);
        $this->assertNull($user3);
    }
}
