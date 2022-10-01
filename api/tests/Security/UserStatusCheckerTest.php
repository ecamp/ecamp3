<?php

namespace App\Tests\Security\Voter;

use App\Entity\User;
use App\Security\UserStatusChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 */
class UserStatusCheckerTest extends TestCase {
    private UserStatusChecker $userChecker;

    public function setUp(): void {
        parent::setUp();

        $this->userChecker = new UserStatusChecker();
    }

    public function testNoActionForNonUserEntity() {
        $this->expectNotToPerformAssertions();
        $user = $this->createMock(UserInterface::class);
        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    public function testNoActionForActivatedUser() {
        $this->expectNotToPerformAssertions();

        $user = new User();
        $user->state = User::STATE_ACTIVATED;

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    public function testThrowExceptionForDeletedUser() {
        $this->expectException(CustomUserMessageAccountStatusException::class);

        $user = new User();
        $user->state = User::STATE_DELETED;

        $this->userChecker->checkPreAuth($user);
    }

    public function testThrowExceptionForInactivatedUser() {
        $this->expectException(CustomUserMessageAccountStatusException::class);

        $user = new User();
        $user->state = User::STATE_REGISTERED;

        $this->userChecker->checkPreAuth($user);
    }
}
