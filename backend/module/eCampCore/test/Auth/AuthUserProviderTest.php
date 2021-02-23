<?php

namespace eCamp\CoreTest\Auth;

use eCamp\Core\Auth\AuthUserProvider;
use eCamp\Core\EntityService\UserService;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;
use Laminas\Authentication\AuthenticationService;

/**
 * @internal
 */
class AuthUserProviderTest extends AbstractDatabaseTestCase {
    public function testAuthUser(): void {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        $user = $userService->create((object) [
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch',
        ]);

        $authenticationService = new AuthenticationService();
        $authenticationService->getStorage()->write($user->getId());

        /** @var AuthUserProvider $authUserProvider */
        $authUserProvider = \eCampApp::GetService(AuthUserProvider::class);
        $authUser = $authUserProvider->getAuthUser();

        $this->assertNotNull($authUser);
        $this->assertEquals($user->getId(), $authUser->getId());
    }

    public function testNoAuthUser(): void {
        $authenticationService = new AuthenticationService();
        $authenticationService->clearIdentity();

        /** @var AuthUserProvider $authUserProvider */
        $authUserProvider = \eCampApp::GetService(AuthUserProvider::class);
        $authUser = $authUserProvider->getAuthUser();

        $this->assertNull($authUser);
    }
}
