<?php

namespace eCamp\CoreTest\Service;

use eCamp\Core\Entity\User;
use eCamp\Core\Service\UserService;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class UserServiceTest extends AbstractHttpControllerTestCase
{
    public function setUp() {
        include_once __DIR__ . '/../../../eCampApp.php';
    }


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

        $user = $userService->create([
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch'
        ]);

        $userId = $user->getId();

        $user2 = $userService->fetch($userId);
        $this->assertEquals($user, $user2);

        $user3 = $userService->fetch(0);
        $this->assertNull($user3);
    }

}