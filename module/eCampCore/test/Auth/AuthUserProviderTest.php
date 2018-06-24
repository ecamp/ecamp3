<?php

namespace eCamp\CoreTest\Auth;

use eCamp\Core\Auth\AuthUserProvider;
use eCamp\Core\EntityService\UserService;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;
use Zend\Authentication\AuthenticationService;

class AuthUserProviderTest extends AbstractDatabaseTestCase {
    public function testAuthUser() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        $user = $userService->create((object)[
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch'
        ]);

        $authService = new AuthenticationService();
        $authService->getStorage()->write($user->getId());


        /** @var AuthUserProvider $authUserProvider */
        $authUserProvider = \eCampApp::GetService(AuthUserProvider::class);
        $authUser = $authUserProvider->getAuthUser();

        $this->assertNotNull($authUser);
        $this->assertEquals($user->getId(), $authUser->getId());
    }

    public function testNoAuthUser() {
        $authService = new AuthenticationService();
        $authService->clearIdentity();

        /** @var AuthUserProvider $authUserProvider */
        $authUserProvider = \eCampApp::GetService(AuthUserProvider::class);
        $authUser = $authUserProvider->getAuthUser();

        $this->assertNull($authUser);
    }
}
